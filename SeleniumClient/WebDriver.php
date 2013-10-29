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
	private $_options = null;
	
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
	 * Gets Options object
	 * @return SeleniumClient\Options
	 */
	public function manage()
	{
		if(!$this->_options)
		{
			$this->_options = new Options($this);
		}
		return $this->_options;
	}

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
		return $results['value'];
	}
	
	/**
	 * Gets information on current selenium sessions
	 * @return Array of current sessions in hub
	 */
	public function getCurrentSessions()
	{
		$command = new Commands\GetSessions($this);			
		$results = $command->execute();		
		return $results['value'];
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
		return $results['value'];
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
		return $results;
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
		return $results['value'];
	}
	
	/**
	 * Gets current page title
	 * @return String
	 */
	public function title()
	{
		$command = new Commands\Title($this);	
		$results = $command->execute();	
		return $results['value'];	
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
		
		if (isset($results['value']) && trim($results['value']) != "")
		{
			if (!file_exists($screenshotsDirectory . "/" . $this->_sessionId)) { mkdir($screenshotsDirectory . "/" . $this->_sessionId, 0777, true); }
			
			$fileName = date ("YmdHmsu") . "-" . (count(glob($screenshotsDirectory . "/" . $this->_sessionId . "/*.png")) + 1) .".png";
			
			file_put_contents($screenshotsDirectory . "/" . $this->_sessionId . "/" .$fileName, base64_decode($results['value']));
			
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
            return new WebElement($this, $results['value']['ELEMENT']);
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
		return new WebElement($this, $results['value']['ELEMENT']);
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
		return $results['value'];
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
     * Maximizes current Window
     */
    public function maximizeCurrentWindow() {
     	$commannd = new Commands\WindowMaximize($this, null, array('window_handle' => 'current'));
        $commannd->execute();
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
		return $results['value'];		
	}
	
	/**
	 * Gets a list of available windows in current session
	 * @return Array
	 */
	public function getCurrentWindowHandles()
	{
		$command = new Commands\WindowHandles($this);			
		$results = $command->execute();
		return $results['value'];				
	}
	
	/**
	 * Sets current window size
	 * @param Integer $width
	 * @param Integer $height
	 */
	public function setCurrentWindowSize($width, $height)
	{
		$params = array ('width' => $width, 'height' => $height);
		$command = new Commands\SetWindowSize($this, $params, array('window_handle' => 'current'));			
		$command->execute();
	}
	
	/**
	 * Gets current window's size
	 * @return Array
	 */
	public function getCurrentWindowSize()
	{
		$command = new Commands\GetWindowSize($this, null,  array('window_handle' => 'current'));			
		$results = $command->execute();
		return $results['value'];
	}
	
	/**
	 * Sets current window's position
	 * @param Integer $x
	 * @param Integer $y
	 */
	public function setCurrentWindowPosition($x, $y)
	{
		$params = array ('x' => $x, 'y' => $y);
		$command = new Commands\SetWindowPosition($this, $params,  array('window_handle' => 'current'));			
		$command->execute();
	}
	
	/**
	 * Gets current window's position
	 * @return Array
	 */
	public function getCurrentWindowPosition()
	{
		$command = new Commands\GetWindowPosition($this, null, array('window_handle' => 'current'));
		$results = $command->execute(); 
		return $results['value'];	
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
		return $results['value'];
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
