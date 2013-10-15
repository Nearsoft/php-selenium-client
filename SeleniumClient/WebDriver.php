<?php
// Copyright 2012-present Nearsoft, Inc

// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at

// http://www.apache.org/licenses/LICENSE-2.0

// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

namespace SeleniumClient;

use SeleniumClient\DesiredCapabilities;
use SeleniumClient\Commands\Command;
use SeleniumClient\Http\HttpClient;
use SeleniumClient\Http\HttpFactory;
use SeleniumClient\Http\SeleniumInvalidSelectorException;
use SeleniumClient\Http\SeleniumNoSuchElementException;

require_once __DIR__ . '/By.php';
require_once __DIR__ . '/DesiredCapabilities.php';
require_once __DIR__ . '/Http/Exceptions.php';
require_once __DIR__ . '/Http/HttpFactory.php';
require_once __DIR__ . '/Http/HttpClient.php';
require_once __DIR__ . '/TargetLocator.php';
require_once __DIR__ . '/WebElement.php';
require_once __DIR__ . '/Commands/Commands.php';

/**
 * @param string $selectorValue
 * @param string $selectorDefinition
 * @param bool $polling
 * 
 * @method \SeleniumClient\WebElement findElementByCssSelector($selectorValue, $polling=false)
 * @method \SeleniumClient\WebElement findElementById($selectorValue, $polling=false)
 * @method \SeleniumClient\WebElement findElementByJsSelector($selectorValue, $selectorDefinition='$', $polling=false)
 * @method \SeleniumClient\WebElement findElementByLinkText($selectorValue, $polling=false)
 * @method \SeleniumClient\WebElement findElementByName($selectorValue, $polling=false)
 * @method \SeleniumClient\WebElement findElementByPartialLinkText($selectorValue, $polling=false)
 * @method \SeleniumClient\WebElement findElementByTagName($selectorValue, $polling=false)
 * @method \SeleniumClient\WebElement findElementByXPath($selectorValue, $polling=false)
 *
 * @method \SeleniumClient\WebElement[] findElementsByCssSelector($selectorValue, $polling=false)
 * @method \SeleniumClient\WebElement[] findElementsById($selectorValue, $polling=false)
 * @method \SeleniumClient\WebElement[] findElementsByJsSelector($selectorValue, $selectorDefinition='$', $polling=false)
 * @method \SeleniumClient\WebElement[] findElementsByLinkText($selectorValue, $polling=false)
 * @method \SeleniumClient\WebElement[] findElementsByName($selectorValue, $polling=false)
 * @method \SeleniumClient\WebElement[] findElementsByPartialLinkText($selectorValue, $polling=false)
 * @method \SeleniumClient\WebElement[] findElementsByTagName($selectorValue, $polling=false)
 * @method \SeleniumClient\WebElement[] findElementsByXPath($selectorValue, $polling=false)
 */
class WebDriver
{
	private $_hubUrl = null;
	private $_sessionId = null;
	private $_screenshotsDirectory = null;
	private $_environment = HttpFactory::PRODUCTIONMODE;
	private $_capabilities = null;
	private $_httpClient = null;
	
	/**
	 * @param DesiredCapabilities $desiredCapabilities
	 * @param String $host
	 * @param Integer $port
	 */
	public function __construct(DesiredCapabilities $desiredCapabilities = null, $host = "http://localhost", $port = 4444)
	{
		$this->_hubUrl = $host . ":" . strval($port) . "/wd/hub";		
		if(!isset($desiredCapabilities)) { $desiredCapabilities = new DesiredCapabilities("firefox"); }
		$this->_httpClient = HttpFactory::getClient($this->_environment);		
		$this->startSession($desiredCapabilities);
	}

    /**
     * @param string $name
     * @param array  $args
     * @return mixed
     * @throws \Exception
     */
    public function __call( $name, array $args )
    {
        $arr = explode( 'By', $name );
        $call = $arr[0];
        $by = count( $arr ) > 1 ? lcfirst( $arr[1] ) : '';

        $valid = false;

        switch ( $call ) {
            case 'findElement':
            case 'findElements':
                if ( method_exists( '\\SeleniumClient\\By', $by ) ) {
                    $valid = true;
                }
        }

        if ( !$valid ) {
            throw new \Exception( 'Invalid magic call' );
        }

        $method = new \ReflectionMethod( '\\SeleniumClient\\By', $by );
        $byArgs = array_splice( $args, 0, $method->getNumberOfParameters() );
        array_unshift( $args, $method->invokeArgs( null, $byArgs ) );

        return call_user_func_array( array( $this, $call ), $args );
    }
	
	/**
	 * Set whether production or testing mode for library
	 * @param String $value
	 */
	public function setEnvironment($value) { $this->_environment = $value; }
	
	/**
	 * Get current Selenium environment
	 * @return String
	 */
	public function getEnvironment() {
		return $this->_environment;
	}

	/**
	 * Get HttpClient Object
	 * @return String
	 */
	public function getHttpClient() {
		return $this->_httpClient;
	}

	/**
	 * Get current Selenium Hub url
	 * @return String
	 */
	public function getHubUrl() { return $this->_hubUrl; }
	
	/**
	 * Get assigned session id
	 * @return Integer
	 */
	public function getSessionId() { return $this->_sessionId; }
	
	/**
	 * Get default screenshots directory
	 * @return String
	 */
	public function getScreenShotsDirectory() { return $this->_screenshotsDirectory; }
	
	/**
	 * Sets default screenshots directory for files to be stored in
	 * @param String $value
	 */
	public function setScreenShotsDirectory($value) { $this->_screenshotsDirectory = $value; }
	
	/**
	 * Creates new target locator to be handled
	 * @return \SeleniumClient\TargetLocator
	 */
	public function switchTo() { return new TargetLocator($this); }

	/**
	 * Starts new Selenium session
	 * @param DesiredCapabilities $desiredCapabilities
	 * @throws \Exception
	 */
	private function startSession(DesiredCapabilities $desiredCapabilities)
	{
		if($desiredCapabilities->getBrowserName() == null || trim($desiredCapabilities->getBrowserName()) == '')
		{
			throw new \Exception("Can not start session if browser name is not specified");
		}

		$params = array ('desiredCapabilities' => $desiredCapabilities->getCapabilities());	
		$command = new Commands\StartSession($this, $params);
		$results = $command->execute();	
		$this->_sessionId = $results['sessionId'];
		$this->_capabilities = $this->getCapabilities();
	}
	
	/**
	 * Gets actual capabilities
	 * @return Array of actual capabilities
	 */
	public function getCapabilities()
	{
		$command = new Commands\GetCapabilities($this);		
		$results = $command->execute();
		return $results["value"];
	}
	
	/**
	 * Gets information on current selenium sessions
	 * @return Array of current sessions in hub
	 */
	public function getCurrentSessions()
	{
		$command = new Commands\GetSessions($this);			
		$results =$command->execute();		
		$result = null;
		if (isset ( $results ["value"] )) { $result = $results["value"]; }
		return $result;
	}

	/**
	 * Removes current session
	 */
	public function quit()
	{
		$command = new Commands\Quit($this);
		$command->execute();
	}
	
	/**
	 * Navigates to specified url
	 * @param String $url
	 */
	public function get($url)
	{
		$params = array ('url' => $url);
		$command = new Commands\GetUrl($this,$params);	
		$command->execute();	
	}
	
	/**
	 * Gets current url
	 * @return String
	 */
	public function getCurrentPageUrl()
	{
		$command = new Commands\GetCurrentUrl($this);			
		$results = $command->execute();	

		$result = null;
		if (isset($results["value"]) && trim ($results["value"]) != "") { $result = $results ["value"]; }
		return $result;
	}	
	
	/**
	 * Sets default time for selenium to wait for an element to be present
	 * @param Integer $miliseconds
	 */
	public function setImplicitWait($miliseconds)
	{
		$params = array ('ms' => $miliseconds );
		$command = new Commands\ImplicitWait($this,$params);	
		$command->execute();
	}
	
	/**
	 * Get current server's status
	 * @return Array
	 */
	public function status()
	{
		$command = new Commands\Status($this);			
		$results = $command->execute();		
		$result = null;
		if (is_array($results)) { $result = $results; }
		return $result;
	}	
	
	/**
	 * Navigate forward in history
	 */
	public function forward()
	{
		$command = new Commands\Forward($this);		
		$command->execute();	
	}	
	
	/**
	 * Navigate back in history
	 */
	public function back()
	{
		$command = new Commands\Back($this);		
		$command->execute();	
	}	
	
	/**
	 * Refreshes current page
	 */
	public function refresh()
	{
		$command = new Commands\Refresh($this);		
		$command->execute();	
	}
	
	/**
	 * Gets current page source
	 * @return String
	 */
	public function pageSource()
	{
		$command = new Commands\Source($this);		
		$results = $command->execute();		
		$result = null;
		if (isset($results["value"]) && trim ($results["value"]) != "") { $result = $results["value"]; }
		return $result;
	}
	
	/**
	 * Gets current page title
	 * @return String
	 */
	public function title()
	{
		$command = new Commands\Title($this);	
		$results = $command->execute();	
		
		$result = null;
		if (isset($results["value"]) && trim ($results["value"]) != "") { $result = $results["value"]; }
		return $result;	
	}

	/**
	 * Takes screenshot of current screen, saves it in specified default directory or as specified in parameter
	 * @param String $overrideScreenshotsDirectory
	 * @throws \Exception
	 * @return string
	 */
	public function screenshot($overrideScreenshotsDirectory = null)
	{
		$screenshotsDirectory = null;
		if (isset($overrideScreenshotsDirectory)) { $screenshotsDirectory = $overrideScreenshotsDirectory; }
		else if (isset($this->_screenshotsDirectory)) { $screenshotsDirectory = $this->_screenshotsDirectory; }
		else { throw new \Exception("Must Specify Screenshot Directory"); }
		
		$command = new Commands\Screenshot($this);	

		$results = $command->execute();
		
		if (isset($results["value"]) && trim($results["value"]) != "")
		{
			if (!file_exists($screenshotsDirectory . "/" . $this->_sessionId)) { mkdir($screenshotsDirectory . "/" . $this->_sessionId, 0777, true); }
			
			$fileName = date ("YmdHmsu") . "-" . (count(glob($screenshotsDirectory . "/" . $this->_sessionId . "/*.png")) + 1) .".png";
			
			file_put_contents($screenshotsDirectory . "/" . $this->_sessionId . "/" .$fileName, base64_decode($results["value"]));
			
			return $fileName;
		}

        return null;
	}

    /**
     * Gets an element within current page
     * @param By   $locator
     * @param bool $polling
     * @param int  $elementId
     * @throws Http\SeleniumNoSuchElementException
     * @return \SeleniumClient\WebElement
     */
	public function findElement(By $locator, $polling = false, $elementId = -1)
	{
        if (strpos($locator->getStrategy(), 'js selector ') === 0) {
            $result = $this->findElements($locator, $polling, $elementId);
            if (!$result) {
                throw new SeleniumNoSuchElementException();
            }
            return $result[0];
        } else {
			$params = array ('using' => $locator->getStrategy(), 'value' => $locator->getSelectorValue());
            if ($elementId < 0) {
                 $command = new Commands\Element($this,$params);	
            }
            else
            {
            	 $command = new Commands\ElementInElement($this, $params, array('element_id' => $elementId));	
            }
            $command->setPolling($polling);
            $results = $command->execute();       

            $result = null;
            if (isset($results["value"]["ELEMENT"]) && trim ($results["value"]["ELEMENT"]) != "") { $result = new WebElement($this, $results["value"]["ELEMENT"]); }
            return $result;
        }
	}

    /**
     * Gets elements within current page
     * @param By   $locator
     * @param bool $polling
     * @param int  $elementId
     * @throws SeleniumInvalidSelectorException
     * @return \SeleniumClient\WebElement[]
     */
    public function findElements(By $locator, $polling = false, $elementId = -1)
    {
        if (strpos($locator->getStrategy(), 'js selector ') === 0) {
            $function = substr($locator->getStrategy(), 12);
            $script = "return typeof window.{$function};";
            $valid = $this->executeScript($script) == 'function';
            $selector = addslashes($locator->getSelectorValue());

            if (!$valid) {
                throw new SeleniumInvalidSelectorException('The selectorElement is not defined');
            }

            if ($elementId >= 0) {
                // todo refactor child selection strategy to separate classes
                if (strpos($function, 'document.') === 0) {
                    // assume child.$function($selector)
                    $function = substr($function, 9);
                    $script = sprintf('return arguments[0].%s("%s")', $function, $selector);
                } else {
                    // assume $function($selector, child)
                    $script = sprintf('return %s("%s", arguments[0])', $function, $selector);
                }
                $args = array(array('ELEMENT' => $elementId));
            } else {
                $script = sprintf('return %s("%s")', $function, $selector);
                $args = array();
            }

            $params = array('script' => $script, 'args' => $args);
            $command = new Commands\ExecuteScript($this, $params);
            $results = $command->execute();
        } else {
            $params = array('using' => $locator->getStrategy(), 'value' => $locator->getSelectorValue());

            if($elementId >= 0)
            {
				$command = new Commands\ElementsInElement($this, $params, array('element_id' => $elementId));
            }
            else
            {
            	$command = new Commands\Elements($this, $params);
            }
            
            $results = $command->execute();
        }

        $webElements = array();
        
        if (isset($results['value']) && is_array($results['value'])) {
            foreach ($results['value'] as $element) {
                $webElements[] = new WebElement($this, is_array($element) ? $element['ELEMENT'] : $element);
            }
        }
        
        return $webElements ?: null;
    }
	
	/**
	 * Gets element that is currently focused
	 * @return \SeleniumClient\WebElement
	 */
	public function getActiveElement()
	{
		$command = new Commands\ActiveElement($this);	
		$results = $command->execute();	
		
		$result = null;
		if (isset($results["value"]["ELEMENT"]) && trim ($results["value"]["ELEMENT"]) != "") { $result = new WebElement($this, $results["value"]["ELEMENT"]); }
		return $result;
	}
	
	/**
	 * Stops the process until an element is found
	 * @param By $locator
	 * @param Integer $timeOutSeconds
	 * @return \SeleniumClient\WebElement
	 */
	public function waitForElementUntilIsPresent(By $locator, $timeOutSeconds = 5)
	{
		$wait = new WebDriverWait($timeOutSeconds);
		$dynamicElement = $wait->until($this, "findElement", array($locator, true));
		return $dynamicElement;
	}

	/**
	 * Stops the process until an element is not found
	 * @param By $locator
	 * @param Integer $timeOutSeconds
	 * @return boolean true when element is gone, false if element is still there
	 */
	public function waitForElementUntilIsNotPresent(By $locator, $timeOutSeconds = 5)
	{
		for ($second = 0; ; $second++)
		{
			if ($second >= $timeOutSeconds) return false;
			$result = ($this->findElement($locator, true) === null);
			if ($result)
			{
				return true;
			}
			sleep(1);
		}
        return false;
	}

	/**
	 * Send text to element
	 * @param Integer $elementId
	 * @param String $text
	 */
	public function webElementSendKeys($elementId, $text)
	{
		$params = array('value' => $this->getCharArray($text));
		$command = new Commands\ElementValue($this, $params , array('element_id' => $elementId));	
		$command->execute();	
	}
	
	/**
	 * Returns array of chars from String
	 * @param String $text
	 * @return array
	 */
	private function getCharArray($text)
	{
		$encoding = \mb_detect_encoding($text);
		$len = \mb_strlen($text, $encoding);
		$ret = array();
		while($len) {
			$ret[] = \mb_substr($text, 0, 1, $encoding);
			$text = \mb_substr($text, 1, $len, $encoding);
			$len = \mb_strlen($text, $encoding);
		}
		return $ret;
	}
	
	/**
	 * Gets element's visible text
	 * @param Integer $elementId
	 * @return String
	 */
	public function webElementGetText($elementId)
	{
		$command = new Commands\ElementText($this, null , array('element_id' => $elementId));		
		$results = $command->execute();
		
		$result = null;
		if (isset($results["value"])) { $result = $results ["value"]; }
		return $result;	
	}
	
	/**
	 * Gets element's tag name
	 * @param Integer $elementId
	 * @return String
	 */
	public function webElementGetTagName($elementId)
	{
		$command = new Commands\ElementTagName($this, null , array('element_id' => $elementId));		
		$results = $command->execute();
		
		$result = null;
		if (isset($results["value"]) && trim($results["value"]) != "")  { $result = trim($results ["value"]); }
		return $result;
	}

	/**
	 * Gets element's specified attribute
	 * @param Integer $elementId
	 * @param String $attributeName
	 * @return String
	 */
	public function webElementGetAttribute($elementId, $attributeName)
	{
		$command = new Commands\ElementAttribute($this, null , array('element_id' => $elementId, 'attribute_name' => $attributeName));		
		$results = $command->execute();
		
		$result = null;
		if (isset($results["value"]) && trim($results["value"]) != "") { $result = trim($results["value"]); }
		return $result;
	}
	
	/**
	 * Gets whether an element is selected
	 * @param Integer $elementId
	 * @return boolean
	 */
	public function webElementIsSelected($elementId)
	{
		$command = new Commands\ElementIsSelected($this, null , array('element_id' => $elementId));		
		$results = $command->execute();
		
		$result = false;
		if(trim($results ["value"]) == "1") { $result = true; }
		return $result;		
	}	
	
	/**
	 * Gets whether an element is currently displayed
	 * @param Integer $elementId
	 * @return boolean
	 */
	public function webElementIsDisplayed($elementId)
	{
		$command = new Commands\ElementIsDisplayed($this, null , array('element_id' => $elementId));		
		$results = $command->execute();
		
		$result = false;
		if(trim($results ["value"]) == "1") { $result = true; }
		return $result;	
	}
	
	/**
	 * Gets whether an element is currently enabled
	 * @param Integer $elementId
	 * @return boolean
	 */
	public function webElementIsEnabled($elementId)
	{
		$command = new Commands\ElementIsEnabled($this, null , array('element_id' => $elementId));		
		$results = $command->execute();
		
		$result = false;
		if(trim($results ["value"]) == "1") { $result = true; }
		return $result;
	}	
	
	/**
	 * Clear element's value
	 * @param Integer $elementId
	 */
	public function webElementClear($elementId)
	{
		$command = new Commands\ClearElement($this, null , array('element_id' => $elementId));		
		$command->execute();
	}
		
	/**
	 * Clicks on an element
	 * @param Integer $elementId
	 */
	public function webElementClick($elementId)
	{
		$command = new Commands\ClickElement($this, null , array('element_id' => $elementId));		
		$command->execute();
	}
	
	/**
	 * Execute form submit from element
	 * @param Integer $elementId
	 */
	public function webElementSubmit($elementId)
	{
		$command = new Commands\ElementSubmit($this, null , array('element_id' => $elementId));		
		$command->execute();
	}
	
	/**
	 * Gets element's description
	 * @param Integer $elementId
	 * @return Array
	 */
	public function webElementDescribe($elementId)
	{
		$command = new Commands\DescribeElement($this, null , array('element_id' => $elementId));		
		$results = $command->execute();
		
		$result = null;
		if (isset($results["value"]) && is_array($results["value"])) { $result = $results ["value"]; }
		return $result;
	}
	
	/**
	 * Find an element within another element
     * @deprecated
	 * @param Integer $elementId
	 * @param By $locator
	 * @param Boolean $polling
	 * @return \SeleniumClient\WebElement
	 */
	public function webElementFindElement($elementId, By $locator, $polling = false)
	{
        return $this->findElement($locator, $polling, $elementId);
	}
	
	/**
	 * Find elements within another element
     * @deprecated
	 * @param Integer $elementId
	 * @param By $locator
	 * @param Boolean $polling
	 * @return \SeleniumClient\WebElement 
	 */
	public function webElementFindElements($elementId, By $locator, $polling = false)
	{
        return $this->findElements($locator, $polling, $elementId);
	}
	
	/**
	 * Gets element's coordinates
	 * @param Integer $elementId
	 * @return Array
	 */
	public function webElementGetCoordinates($elementId)
	{
		$command = new Commands\ElementLocation($this, null , array('element_id' => $elementId));		
		$results = $command->execute();
		
		$result = null;
		if (isset($results["value"]) && is_array($results["value"])) { $result = $results ["value"]; }
		return $result;		
	}
	
	/**
	 * Gets element's coordinates after scrolling
	 * @param Integer $elementId
	 * @return Array
	 */
	public function webElementGetLocationOnScreenOnceScrolledIntoView($elementId)
	{
		$command = new Commands\ElementLocationView($this, null , array('element_id' => $elementId));		
		$results = $command->execute();
		
		$result = null;
		if (isset($results["value"]) && is_array($results["value"])) {
			$result = $results ["value"];
		}
		return $result;		
	}

	/**
	 * Sets page_load timeout
	 * @param int $miliseconds
	 */
	public function setPageLoadTimeout($miliseconds)
	{
		$params = array ('type' => 'page load','ms' => $miliseconds );
		$command = new Commands\LoadTimeout($this, $params);		
		$command->execute();
	}

	/**
	 * Set's Async Script timeout
	 * @param Integer $miliseconds
	 */
	public function setAsyncScriptTimeout($miliseconds)
	{
		$params = array('ms' => $miliseconds);
		$command = new Commands\AsyncScriptTimeout($this, $params);		
		$command->execute();
	}
	
	/**
	 * Executes javascript on page
	 * @param String $script
	 * @param Boolean $async
	 * @param Array $args
	 * @throws \Exception
	 * @return String
	 */
	private function executeScriptInternal($script, $async, $args)
	{
		if (!isset($this->_capabilities['javascriptEnabled']) || trim($this->_capabilities['javascriptEnabled']) != "1" ) { throw new \Exception("You must be using an underlying instance of WebDriver that supports executing javascript"); }
				
		$params = array ('script' => $script, 'args' => array());

        foreach ((array)$args as $arg) {
            if ($arg instanceof WebElement) {
                $arg = array('ELEMENT' => $arg->getElementId());
            }
            $params['args'][] = $arg;
        }
		
		if($async === true)
		{
			$command = new Commands\ExecuteAsyncScript($this, $params);
		}
		else
		{
			$command = new Commands\ExecuteScript($this, $params);
		}

		$results = $command->execute();

		$result = null;
		if (isset($results ["value"])) { $result = $results ["value"]; }
		return $result;
	}

	/**
	 * Executes javascript on page
	 * @param String $script
	 * @param Array $args
	 * @return String
	 */
	public function executeScript($script, $args = null) { return $this->executeScriptInternal($script, false , $args); }
	
	/**
	 * Execute async javascript on page
	 * @param String $script
	 * @param Array $args
	 * @return String
	 */
	public function executeAsyncScript($script, $args = null) { return $this->executeScriptInternal($script, true , $args); }

	/**
	 * Focus on specified frame
	 * @param String $frameId
	 */
	public function getFrame($frameId)
	{
		//frameId can be string, int or array

		$params = array ('id' => $frameId);
		$command = new Commands\Frame($this, $params);		
		$command->execute();
	}
	
	/**
	 * Changes focus to specified window
	 * @param String $name
	 */
	public function getWindow($name)
	{
		$params = array ('name' => $name); //name parameter could be window name or window handle
		$command = new Commands\Window($this, $params);		
		$command->execute();
	}
	
	/**
	 * Closes current window
	 */
	public function closeCurrentWindow()
	{
		$command = new Commands\CloseWindow($this);		
		$command->execute();
	}
	
	/**
	 * Gets current window's identifier
	 * @return String
	 */
	public function getCurrentWindowHandle()
	{
		$command = new Commands\WindowHandle($this);			
		$results = $command->execute();
		
		$result = null;
		if (isset($results["value"])) { $result = $results ["value"]; }
		return $result;		
	}
	
	/**
	 * Gets a list of available windows in current session
	 * @return Array
	 */
	public function getCurrentWindowHandles()
	{
		$command = new Commands\WindowHandles($this);			
		$results = $command->execute();

		$result = null;
		if (isset($results["value"]) && is_array($results["value"])) { $result = $results ["value"]; }
		return $result;				
	}
	
	/**
	 * Sets current window size
	 * @param Integer $width
	 * @param Integer $height
	 */
	public function setCurrentWindowSize($width, $height)
	{
		$windowHandle = $this->getCurrentWindowHandle();
		$this->setWindowSize($windowHandle, $width, $height);
	}	
	
	/**
	 * Sets specified window's size
	 * @param String $windowHandle
	 * @param Integer $width
	 * @param Integer $height
	 */
	public function setWindowSize($windowHandle, $width, $height)
	{
		$params = array ('width' => $width, 'height' => $height);
		$command = new Commands\SetWindowSize($this, $params, array('window_handle' => $windowHandle));			
		$command->execute();
	}
	
	/**
	 * Gets current window's size
	 * @return Array
	 */
	public function getCurrentWindowSize()
	{
		$windowHandle = $this->getCurrentWindowHandle();
		return $this->getWindowSize($windowHandle);
	}
	
	/**
	 * Gets specified window's size
	 * @param String $windowHandle
	 * @return Array
	 */
	public function getWindowSize($windowHandle)
	{
		$command = new Commands\GetWindowSize($this, null, array('window_handle' => $windowHandle));			
		$results = $command->execute();
		
		$result = null;
		if (isset($results["value"]) && is_array($results["value"])) { $result = $results ["value"]; }
		return $result;
	}
	
	/**
	 * Sets current window's position
	 * @param Integer $x
	 * @param Integer $y
	 */
	public function setCurrentWindowPosition($x, $y)
	{
		$windowHandle = $this->getCurrentWindowHandle();
		$this->setWindowPosition($windowHandle,$x, $y);
	}
	
	/**
	 * Sets specified window's position
	 * @param String $windowHandle
	 * @param Integer $x
	 * @param Integer $y
	 */
	public function setWindowPosition($windowHandle, $x, $y)
	{
		$params = array ('x' => $x, 'y' => $y);
		$command = new Commands\SetWindowPosition($this, $params, array('window_handle' => $windowHandle));			
		$command->execute();
	}
	
	/**
	 * Gets current window's position
	 * @return Array
	 */
	public function getCurrentWindowPosition()
	{
		$windowHandle = $this->getCurrentWindowHandle();
		return $this->getWindowPosition($windowHandle);
	}
	
	/**
	 * Gets specified window's position
	 * @param String $windowHandle
	 * @return Array
	 */
	public function getWindowPosition($windowHandle)
	{
		$command = new Commands\GetWindowPosition($this, null, array('window_handle' => $windowHandle));
		$results = $command->execute(); 
		
		$result = null;
		if (isset($results["value"]) && is_array($results["value"])) { $result = $results ["value"]; }
		return $result;		
	}

	/**
	 * Sets cookie
	 * @param String $name
	 * @param String $value
	 * @param String $path
	 * @param String $domain
	 * @param Boolean $secure
	 * @param Integer $expiry
	 */
	public function setCookie($name, $value, $path = null, $domain = null, $secure = null, $expiry = null)
	{
		$cookie = new Cookie($name, $value, $path, $domain, $secure, $expiry);
		$params = array ('cookie' => $cookie->getArray());
		$command = new Commands\SetCookie($this, $params);
		$results = $command->execute(); 		
	}
	
	/**
	 * Gets current cookies
	 * @return Array
	 */
	public function getCurrentCookies()
	{
		$command = new Commands\GetCookies($this);
		$results = $command->execute(); 		
		$result = null;
		if (isset($results["value"]) && is_array($results["value"])) { $result = $results ["value"]; }
		return $result;		
	}	
	
	/**
	 * Remove cookies
	 * @param String $cookieName
	 */
	public function clearCookie($cookieName)
	{
		$command = new Commands\ClearCookie($this, null, array('cookie_name' => $cookieName));
		$command->execute(); 			
	}
	
	/**
	 * Removes all current cookies
	 */
	public function clearCurrentCookies()
	{
		$command = new Commands\ClearCookies($this);
		$command->execute(); 	
	}

	/**
	 * Sends false to current alert
	 */
	public function dismissAlert()
	{
		$command = new Commands\DismissAlert($this);
		$command->execute(); 	
	}

	/**
	 * Sends true to current alert
	 */
	public function acceptAlert()
	{
		$command = new Commands\AcceptAlert($this);
		$command->execute(); 	
	}

	/**
	 * Gets current alert's text
	 * @return String
	 */
	public function getAlertText()
	{
		$command = new Commands\GetAlertText($this);
		$results = $command->execute(); 	
		
		$result = null;
		if (isset($results["value"])) { $result = $results["value"]; }
		return $result;
	}
	
	/**
	 * Sends text to alert input
	 * @param String $value
	 */
	public function setAlertValue($value)
	{
		if(is_string($value)){
			$params = array ('text' => $value);
			$command = new Commands\SetAlertText($this, $params);
			$command->execute();
		}
		else{
			throw new \Exception("Value must be a string");
		}
	}
}
