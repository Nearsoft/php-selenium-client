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

namespace Nearsoft\SeleniumClient;

use Nearsoft\SeleniumClient\DesiredCapabilities;
use Nearsoft\SeleniumClient\Commands\Command;
use Nearsoft\SeleniumClient\Http\HttpClient;
use Nearsoft\SeleniumClient\Http\HttpFactory;


/**
 * Class WebDriver
 * @package Nearsoft\SeleniumClient
 *
 * @method Nearsoft\SeleniumClient\Navigation navigationBack()
 * @method Nearsoft\SeleniumClient\Navigation navigationForward()
 * @method Nearsoft\SeleniumClient\Navigation navigationRefresh()
 * @method Nearsoft\SeleniumClient\Navigation navigationTo($url)
 *
 * @method Nearsoft\SeleniumClient\Window windowMaximize()
 * @method Nearsoft\SeleniumClient\Window windowGetSize()
 * @method Nearsoft\SeleniumClient\Window windowGetPosition()
 * @method Nearsoft\SeleniumClient\Window windowSetSize($width, $height)
 * @method Nearsoft\SeleniumClient\Window windowSetPosition($x, $y)
 *
 * @method Nearsoft\SeleniumClient\TargetLocator switchToWindow($identifier)
 * @method Nearsoft\SeleniumClient\TargetLocator switchToFrame($identifier)
 *
 * @method Nearsoft\SeleniumClient\WebElement findElementByCssSelector($selectorValue)
 * @method Nearsoft\SeleniumClient\WebElement findElementById($selectorValue)
 * @method Nearsoft\SeleniumClient\WebElement findElementByJsSelector($selectorValue, $selectorDefinition='$')
 * @method Nearsoft\SeleniumClient\WebElement findElementByLinkText($selectorValue)
 * @method Nearsoft\SeleniumClient\WebElement findElementByName($selectorValue)
 * @method Nearsoft\SeleniumClient\WebElement findElementByPartialLinkText($selectorValue)
 * @method Nearsoft\SeleniumClient\WebElement findElementByTagName($selectorValue)
 * @method Nearsoft\SeleniumClient\WebElement findElementByXPath($selectorValue)
 *
 * @method Nearsoft\SeleniumClient\WebElement[] findElementsByCssSelector($selectorValue)
 * @method Nearsoft\SeleniumClient\WebElement[] findElementsById($selectorValue)
 * @method Nearsoft\SeleniumClient\WebElement[] findElementsByJsSelector($selectorValue, $selectorDefinition='$')
 * @method Nearsoft\SeleniumClient\WebElement[] findElementsByLinkText($selectorValue)
 * @method Nearsoft\SeleniumClient\WebElement[] findElementsByName($selectorValue)
 * @method Nearsoft\SeleniumClient\WebElement[] findElementsByPartialLinkText($selectorValue)
 * @method Nearsoft\SeleniumClient\WebElement[] findElementsByTagName($selectorValue)
 * @method Nearsoft\SeleniumClient\WebElement[] findElementsByXPath($selectorValue)
 */


class WebDriver
{
	private $_hubUrl               = null;
	private $_sessionId            = null;
	private $_screenshotsDirectory = null;
	private $_environment          = HttpFactory::PRODUCTIONMODE;
	private $_capabilities         = null;
	private $_httpClient           = null;
	private $_options              = null;
    private $_navigate             = null;
    private $_targetLocator        = null;
	
	/**
	 * @param DesiredCapabilities $desiredCapabilities
	 * @param String $host
	 * @param Integer $port
	 */
	public function __construct(DesiredCapabilities $desiredCapabilities = null, $host = "http://localhost", $port = 4444)
	{
		$this->_hubUrl = "{$host}:{$port}/wd/hub";
		isset($desiredCapabilities) ? : $desiredCapabilities = new DesiredCapabilities("firefox");
		$this->_httpClient = HttpFactory::getClient($this->_environment);		
		$this->startSession($desiredCapabilities);
	}

    /**
     * Enables Navigation's methods be invoked as navigationNameMethod
     * Example: navigationRefresh, navigationBack.
     * Enables window's methods be invoked
     * Example: windowMaximize, windowGetPosition.
     * Enables TargetLocator's methods
     * Examples: switchToWindow, switchToFrame
     * Enables findElement and findElements methods be invoked through method missing.
     * The methods should be invoked with the format 'findElementBy<strategy>'.
     * Arguments should match those required by findElement and findElements methods.
     * i.e. findElementByCssSelector, findElementByTagName, findElementsByXPath
     * @param string $name
     * @param array  $args
     * @return mixed
     * @throws \Exception
     */
    public function __call( $name, array $args )
    {
        if( strpos($name, "navigation") === 0 ) {
            $this->callNavigationMethods($name, $args);
            return;
        }

        else if( strpos($name, "window") === 0 ) {
          $values = $this->callWindowMethods($name, $args);
          return $values;
        }

        else if ( strpos($name, "switchTo") === 0 ) {
            $values = $this->callSwitchTo($name, $args);
            return $values;
        }

        else if( strpos($name, 'findElement') === 0 ){
            return $this->callFindElement($name, $args);
        }

        else {
            throw new \Exception( 'Invalid magic call: '.$name );
        }

    }

    /**
     * Call Navigation Methods
     * @param $name
     * @param $args
     * @throws \Exception
     */
    private function callNavigationMethods($name, $args)
    {
        $method = lcfirst(substr($name, 10));
        switch ( $method ) {
            case 'back':
            case 'forward':
            case 'refresh':
                call_user_func( array ($this->_navigate, $method) );
                break;
            case 'to':
                call_user_func( array ($this->_navigate, $method),$args[0]);
                break;
            default: throw new \Exception( 'Invalid magic call: '.$name );
        }
        return;
    }

    /**
     * Call Navigation Methods
     * @param $name
     * @param $args
     * @return array
     * @throws \Exception
     */
    private function callWindowMethods($name, $args)
    {
        $method = lcfirst(substr($name, 6));
        switch ($method) {
            case 'maximize':
            case 'getPosition':
            case 'getSize':
                $values = call_user_func( array($this->manage()->window(), $method));
                break;
            case 'setPosition':
            case 'setSize':
                $values = call_user_func( array($this->manage()->window(),$method),$args[0],$args[1]);
                break;
            default: throw new \Exception ( 'Invalid magic call: '.$name);
        }
        return $values;
    }

    /**
     * Call Target Locator Methods
     * @param $name
     * @param array $args
     * @return mixed
     * @throws \Exception
     */
    private function callSwitchTo($name, array $args)
    {
        $method = lcfirst(substr($name, 8));
        switch($method){
            case 'window':
            case 'frame':
                $values = call_user_func( array($this->switchTo(), $method),$args[0]);
                break;
            default: throw new \Exception ('Invalid magic call:'. $name);
        }
        return $values;
    }

    /**Call findElement and findElement methods
     * @param $name
     * @param array $args
     * @return mixed
     * @throws \Exception
     */
    private function callFindElement($name, array $args)
    {
        $arr = explode( 'By', $name );
        $call = $arr[0];
        $by = count( $arr ) > 1 ? lcfirst( $arr[1] ) : '';
        $valid = false;

        switch ( $call ) {
            case 'findElement':
            case 'findElements':
                if ( method_exists( 'Nearsoft\\SeleniumClient\\By', $by ) ) {
                    $valid = true;
                }
        }

        if ( !$valid ) {
            throw new \Exception( 'Invalid magic call: '.$name );
        }

        $method = new \ReflectionMethod( 'Nearsoft\\SeleniumClient\\By', $by );
        $byArgs = array_splice( $args, 0, $method->getNumberOfParameters() );
        array_unshift( $args, $method->invokeArgs( null, $byArgs ) );
        $element = call_user_func_array( array( $this, $call ), $args );
        return  $element;
    }

    /**
     * Delegated addCookies to Class Options
     * @param $name
     * @param $value
     * @param null $path
     * @param null $domain
     * @param null $secure
     * @param null $expiry
     */
    public function addCookie($name, $value, $path = null, $domain = null, $secure = null, $expiry = null)
    {
        $this->manage()->addCookie($name, $value, $path, $domain, $secure, $expiry);
    }

    /**
     * Delegated getCookies to Class Options
     * @return Array
     */
    public function getCookies() { return $this->manage()->getCookies();}

    /**Delegated method getCookieNamed to Class Options
     * @param $cookieName
     * @return mixed
     */
    public function getCookieNamed($cookieName) { return $this->manage()->getCookieNamed($cookieName); }

    /**
     * Delegated method deleteCookieName to Class Options
     * @param $cookieName
     */
    public function deleteCookieNamed($cookieName) { $this->manage()->deleteCookieNamed($cookieName); }


    /**
     * Delegated method deleteAllCookies to Class Options
     */
    public function deleteAllCookies() { $this->manage()->deleteAllCookies(); }


	/**
	 * Set whether production or testing mode for library
	 * @param String $value
	 */
	public function setEnvironment($value) { $this->_environment = $value; }
	
	/**
	 * Get current Selenium environment
	 * @return String
	 */
	public function getEnvironment() { return $this->_environment; }

	/**
	 * Get HttpClient Object
	 * @return String
	 */
	public function getHttpClient()  { return $this->_httpClient; }

	/**
	 * Get current Selenium Hub url
	 * @return String
	 */
	public function getHubUrl() { return $this->_hubUrl; }


    /**
     * Get Navigation object
     * @return Nearsoft\Selenium\Navigation
     */
    public function navigate()
    {
        isset($this->_navigate) ? : $this->setNavigate(new Navigation($this));
        return $this->_navigate;
    }

    /**Set Navigation
     * @param $navigate
     */
    public function setNavigate($navigate) { $this->_navigate = $navigate; }

	/**
	 * Get assigned session id
	 * @return Integer
	 */
	public function getSessionId() { return $this->_sessionId; }
	
	/**
	 * Sets default screenshots directory for files to be stored in
	 * @param String $value
	 */
	public function setScreenShotsDirectory($value) { $this->_screenshotsDirectory = $value; }

	/**
	 * Get default screenshots directory
	 * @return String
	 */
	public function getScreenShotsDirectory() { return $this->_screenshotsDirectory; }
	

	/**
	 * Gets Options object
	 * @return Nearsoft\SeleniumClient\Options
	 */
	public function manage()
	{
		isset($this->_options) ? : $this->_options = new Options($this);
		return $this->_options;
	}

	/**
	 * Creates new target locator to be handled
	 * @return Nearsoft\SeleniumClient\TargetLocator
	 */
	public function switchTo()
	{
		isset($this->_targetLocator) ? : $this->setSwitchTo(new TargetLocator($this));
		return $this->_targetLocator;
	}

    /**
     * Set Target Locator
     * @param $targetLocator
     */
    public function setSwitchTo($targetLocator) { $this->_targetLocator = $targetLocator; }



	/**
	 * Starts new Selenium session
	 * @param DesiredCapabilities $desiredCapabilities
	 * @throws \Exception
	 */
	private function startSession(DesiredCapabilities $desiredCapabilities)
	{
		if($desiredCapabilities->getBrowserName() == null || trim($desiredCapabilities->getBrowserName()) == '') {
			throw new \Exception("Can not start session if browser name is not specified");
		}

		$params = array ('desiredCapabilities' => $desiredCapabilities->getCapabilities());	
		$command = new Commands\Command($this, 'start_session', $params);
		$results = $command->execute();	
		$this->_sessionId = $results['sessionId'];
		$this->_capabilities = $results['value'];
		return $this->_capabilities;
	}
	
	/**
	 * Gets actual capabilities
	 * @return Array of actual capabilities
	 */
	public function getCapabilities()
	{
		$command = new Commands\Command($this, 'get_capabilities');
		$results = $command->execute();
		return $results['value'];
	}
	
	/**
	 * Gets information on current selenium sessions
	 * @return Array of current sessions in hub
	 */
	public function getCurrentSessions()
	{
		$command = new Commands\Command($this, 'get_sessions');
		$results = $command->execute();		
		return $results['value'];
	}

	/**
	 * Removes current session
	 */
	public function quit()
	{
		$command = new Commands\Command($this, 'quit');
		$command->execute();
	}

	/**
	 * Closes current window
	 */
	public function close()
	{
		$command = new Commands\Command($this, 'close_window');
		$command->execute();
	}
		
	/**
	 * Navigates to specified url
	 * @param String $url
	 */
	public function get($url)
	{
        $navigate = $this->navigate();
        $navigate->to($url);
	}
	
	/**
	 * Gets current url
	 * @return String
	 */
	public function getCurrentUrl()
	{
		$command = new Commands\Command($this, 'get_current_url');
		$results = $command->execute();	
		return $results['value'];
	}	

	/**
	 * Get current server's status
	 * @return Array
	 */
	public function status()
	{
		$command = new Commands\Command($this, 'status');
		$results = $command->execute();		
		return $results;
	}	

	/**
	 * Gets current page source
	 * @return String
	 */
	public function getPageSource()
	{
		$command = new Commands\Command($this, 'source');
		$results = $command->execute();		
		return $results['value'];
	}
	
	/**
	 * Gets current page title
	 * @return String
	 */
	public function getTitle()
	{
		$command = new Commands\Command($this, 'title');
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
		
		$command = new Commands\Command($this, 'screenshot');

		$results = $command->execute();
		
		if (isset($results['value']) && trim($results['value']) != "") {
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
     * @return Nearsoft\SeleniumClient\WebElement
     */
	public function findElement(By $locator, $polling = false, $elementId = -1)
	{
        if (strpos($locator->getStrategy(), 'js selector ') === 0) {
            $result = $this->findElements($locator, $polling, $elementId);
            if (!$result) {
                throw new Exceptions\NoSuchElement();
            }
            return $result[0];
        } else {
			$params = array ('using' => $locator->getStrategy(), 'value' => $locator->getSelectorValue());
            if ($elementId < 0) {
                 $command = new Commands\Command($this, 'element', $params);
            }
            else
            {
            	 $command = new Commands\Command($this, 'element_in_element', $params, array('element_id' => $elementId));
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
     * @throws Exceptions\InvalidSelector
     * @return Nearsoft\SeleniumClient\WebElement[]
     */
    public function findElements(By $locator, $polling = false, $elementId = -1)
    {
        if (strpos($locator->getStrategy(), 'js selector ') === 0) {
            $function = substr($locator->getStrategy(), 12);
            $script = "return typeof window.{$function};";
            $valid = $this->executeScript($script) == 'function';
            $selector = addslashes($locator->getSelectorValue());

            if (!$valid) {
                throw new Exceptions\InvalidSelector('The selectorElement is not defined');
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
            $command = new Commands\Command($this, 'execute_script', $params);
            $results = $command->execute();
        } else {
            $params = array('using' => $locator->getStrategy(), 'value' => $locator->getSelectorValue());

            if($elementId >= 0)
            {
				$command = new Commands\Command($this, 'elements_in_element', $params, array('element_id' => $elementId));
            }
            else
            {
            	$command = new Commands\Command($this, 'elements', $params);
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
	 * Stops the process until an element is found
	 * @param By $locator
	 * @param Integer $timeOutSeconds
	 * @return Nearsoft\SeleniumClient\WebElement
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
			$command = new Commands\Command($this, 'execute_async_script', $params);
		}
		else
		{
			$command = new Commands\Command($this, 'execute_script',  $params);
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
	 * Gets current window's identifier
	 * @return String
	 */
	public function getWindowHandle()
	{
		$command = new Commands\Command($this, 'window_handle');
		$results = $command->execute();
		return $results['value'];		
	}
	
	/**
	 * Gets a list of available windows in current session
	 * @return Array
	 */
	public function getWindowHandles()
	{
		$command = new Commands\Command($this, 'window_handles');
		$results = $command->execute();
		return $results['value'];				
	}	
}
