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

use SeleniumClient\Http\SeleniumStaleElementReferenceException;

/**
 * @param string $value The attribute value
 *
 * @method \SeleniumClient\WebElement setClassName($value) Set element's class name
 * @method \SeleniumClient\WebElement setInnerHTML($value) Set element's inner html
 * @method void setOuterHTML($value) Set element's outer html
 * @method \SeleniumClient\WebElement setText($value) Set element's text
 * @method \SeleniumClient\WebElement setValue($value) Set element's value
 *
 * @method string getClassName() Get element's class name
 * @method string getInnerHTML() Get element's inner html
 * @method string getOuterHTML() Get element's outer html
 * @method string getValue() Get element's value
 */
class WebElement
{
	private $_driver = null;
	private $_elementId = null;
	
	function __construct(WebDriver $driver, $elementId)
	{
		$this->_driver = $driver;
		$this->_elementId = $elementId;
	}

    /**
     * @param string $name
     * @param array  $args
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, array $args)
    {
        $whitelist = array('className', 'innerHTML', 'outerHTML', 'text', 'value');
        $method = lcfirst(substr($name, 3));
        $operation = in_array($method, $whitelist) ? substr($name, 0, 3) : '';

        switch ($operation) {
            case 'set':
                $this->setAttribute($method, $args[0]);
                return ($method != 'outerHTML') ? $this : null;
            case 'get':
                return $this->getAttribute($method);
                break;
            default:
                throw new \Exception('Invalid magic call');
        }
    }
	
	/**
	 * Gets element's id
	 * @return Integer
	 */
	public function getElementId() { return $this->_elementId; }
	
	/**
	 * Send text to element
	 * @param String $text
	 */
	public function sendKeys($text) { $this->_driver->webElementSendKeys($this->_elementId,$text); }
	
	/**
	 * Gets element's visible text
	 * @return String
	 */
	public function getText() { return $this->_driver->webElementGetText($this->_elementId); }
	
	/**
	 * Gets element's tag name
	 * @return String
	 */
	public function getTagName() { return $this->_driver->webElementGetTagName($this->_elementId); }


    /**
     * Test if two element refer to the same DOM element.
     * @param $webElementCompare
     * @return boolean
     */
    public function compareToOther(WebElement $webElementCompare)
    {
        $params = array('element_id' => $this->getElementId(), 'element_id_compare' => $webElementCompare->getElementId());
        $command = new Commands\CompareToOther($this->_driver, null , $params);
        $results = $command->execute();
        return (trim($results['value']) == "1");
    }

    /**
     * Sets element's specified attribute's value
     * @param string $attributeName The element's attribute name
     * @param string $value The value to set the attribute
     * @return $this
     */
    public function setAttribute($attributeName, $value)
    {
        $key = $attributeName == 'text' 
                ? 'var k=typeof arguments[0].textContent!="undefined"?"textContent":"innerText"'
                : sprintf('var k="%s"', addslashes($attributeName));

        $script = sprintf("{$key};arguments[0][k]='%s';return true", addslashes($value));
        $this->_driver->executeScript( $script, array( array( 'ELEMENT' => $this->getElementId() ) ) );
        return $this;
    }

	/**
	 * Gets element's specified attribute's value
	 * @param String $attributeName
	 * @return String
	 */
	public function getAttribute($attributeName)
	{
		return $this->_driver->webElementGetAttribute($this->_elementId, $attributeName);
	}

	/**
	 * Gets whether element is selected
	 * @return Boolean
	 */
	public function isSelected() { return $this->_driver->webElementIsSelected($this->_elementId); }
	
	/**
	 * Gets whether element is displayed
	 * @return Boolean
	 */
	public function isDisplayed() { return $this->_driver->webElementIsDisplayed($this->_elementId); }
	
	/**
	 * Gets whether element is enabled
	 * @return Boolean
	 */
	public function isEnabled() { return $this->_driver->webElementIsEnabled($this->_elementId); }	
	
	/**
	 * Clear current element's text
	 */
	public function clear() { return $this->_driver->webElementClear($this->_elementId); }
	
	/**
	 * Click on element
	 */
	public function click() { $this->_driver->webElementClick($this->_elementId); }
	
	/**
	 * Submit form from element
	 */
	public function submit() { $this->_driver->webElementSubmit($this->_elementId); }
	
	/**
	 * Gets element's description
	 * @return Array
	 */
	public function describe() { return $this->_driver->webElementDescribe($this->_elementId); }

    /**
     * @param string $class
     * @return $this
     */
    public function addClass($class)
    {
        $script = sprintf("if(!arguments[0].className.match(/\b{$class}\b/))arguments[0].className+=' {$class}'");
        $this->_driver->executeScript($script, array(array('ELEMENT' => $this->_elementId)));
        return $this;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function hasClass($class)
    {
        $script = sprintf("return (arguments[0].className.split(' ').indexOf('{$class}') > 0);");
        $result =$this->_driver->executeScript($script, array(array('ELEMENT' => $this->_elementId)));
        return $result;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function removeClass($class)
    {
        $script = sprintf("arguments[0].className = arguments[0].className.replace(/\b{$class}\b/, '').replace(/\s{2,}/, ' ').replace(/^\s+|\s+$/, '')");
        $this->_driver->executeScript($script, array(array('ELEMENT' => $this->_elementId)));
        return $this;
    }

	/**
	 * Get element's coordinates
	 * @return Array
	 */
	public function getCoordinates() {return $this->_driver->webElementGetCoordinates($this->_elementId);}
	
	/**
	 * Get element's coordinates after scrolling
	 * @return Array
	 */
	public function getLocationOnScreenOnceScrolledIntoView() {return $this->_driver->webElementGetLocationOnScreenOnceScrolledIntoView($this->_elementId);}

	/**
	 * Find element within current element
	 * @param By $locator
	 * @param Boolean $polling
	 * @return \SeleniumClient\WebElement
	 */
	public function findElement(By $locator, $polling = false) { return $this->_driver->findElement($locator, $polling, $this->_elementId); }
	
	/**
	 * Find elements within current element
	 * @param By $locator
	 * @param Boolean $polling
	 * @return \SeleniumClient\WebElement[]
	 */
	public function findElements(By $locator, $polling = false) { return $this->_driver->findElements($locator, $polling, $this->_elementId); }
	
	/**
	 * Wait for expected element to be present within current element
	 * @param By $locator
	 * @param Integer $timeOutSeconds
	 * @return mixed
	 */
	public function waitForElementUntilIsPresent(By $locator, $timeOutSeconds = 5)
	{	
		$wait = new WebDriverWait($timeOutSeconds);
		$dynamicElement = $wait->until($this, "findElement", array($locator, true));		
		return $dynamicElement;
	}
	
	/**
	 * Wait for current element to be displayed
	 * @param Integer $timeOutSeconds
	 * @return \SeleniumClient\WebElement
	 */
	public function waitForElementUntilIsDisplayed($timeOutSeconds = 5)
	{
		$wait = new WebDriverWait($timeOutSeconds);
		$element = $wait->until($this, "isDisplayed", array());		
		return $this;
	}
	
	/**
	 * Wait for current element to be enabled
	 * @param Integer $timeOutSeconds
	 * @return \SeleniumClient\WebElement
	 */
	public function waitForElementUntilIsEnabled($timeOutSeconds = 5)
	{
		$wait = new WebDriverWait($timeOutSeconds);
		$element = $wait->until($this, "isEnabled", array());		
		return $this;
	}
	
	/**
	 * Wait until current element's text has changed
	 * @param String $targetText
	 * @param Integer $timeOutSeconds
	 * @throws WebDriverWaitTimeoutException
	 * @return \SeleniumClient\WebElement
	 */
	public function waitForElementUntilTextIsChanged($targetText, $timeOutSeconds = 5)
	{
		$wait = true;
		
		while ($wait)
		{
			$currentText = $this->getText();

			if ($currentText == $targetText) { $wait = false; }
			else if ($timeOutSeconds <= 0) { throw new WebDriverWaitTimeoutException ("Timeout for waitForElementUntilTextIsChange." ); }
			
			sleep(1);
			
			$timeOutSeconds = $timeOutSeconds - 1;
		}
		
		return $this;
	}

	/**
	 * Wait until current element's text equals specified
	 * @param By $locator
	 * @param String $targetText
	 * @param Integer $timeOutSeconds
	 * @throws WebDriverWaitTimeoutException
	 * @return \SeleniumClient\WebElement
	 */
	public function waitForElementUntilIsPresentWithSpecificText(By $locator, $targetText, $timeOutSeconds = 5)
	{
		$dynamicElement = null;
		$wait = true;
		$attempts = $timeOutSeconds;
		
		while ($wait)
		{
			$currentText = null;

			$webDriverWait = new WebDriverWait($timeOutSeconds);
			$dynamicElement = $webDriverWait->until($this, "findElement", array($locator, true));

			try
			{
				$currentText = $dynamicElement->getText();
			}
			catch(SeleniumStaleElementReferenceException $ex)
			{
				//echo "\nError The Objet Disappear, Wait For Element Until Is Present With Specific Text\n";
			}

			if ($currentText == $targetText) { $wait = false; }
			else if ($attempts <= 0) { throw new WebDriverWaitTimeoutException ("Timeout for waitForElementUntilIsPresentAndTextIsChange." ); }
			
			sleep(1);
			
			$attempts = $attempts - 1;
		}
		return $dynamicElement;
	}
}