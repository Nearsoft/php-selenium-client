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
     * Enables setAttribute and getAttribute methods be invoked through method missing.
     * The methods should be invoked with the format 'set/get<Javascript notated attribute name>'.
     * Arguments should match those required by setAttribute and getAttribute methods.
     * i.e. setClassName, getInnerHTML, getClassName
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
	public function sendKeys($text) 
	{ 
		$params = array('value' => $this->getCharArray($text));
		$command = new Commands\Command($this->_driver, 'element_value', $params , array('element_id' => $this->_elementId));
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
	 * @return String
	 */
	public function getText()
	{
		$command = new Commands\Command($this->_driver, 'element_text', null , array('element_id' => $this->_elementId));
		$results = $command->execute();
		return $results['value'];	
	}

	/**
	 * Gets element's tag name
	 * @return String
	 */
	public function getTagName() 
	{
		$command = new Commands\Command($this->_driver, 'element_tag_name', null , array('element_id' => $this->_elementId));
		$results = $command->execute();		
		return $results['value'];
	}


    /**
     * Test if two element refer to the same DOM element.
     * @param WebElement $webElementCompare
     * @return boolean
     */
    public function compareToOther(WebElement $webElementCompare)
    {
        $params = array('element_id' => $this->getElementId(), 'element_id_compare' => $webElementCompare->getElementId());
        $command = new Commands\Command($this->_driver, 'compare_to_other', null , $params);
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
        $this->_driver->executeScript( $script, array( array( 'ELEMENT' => $this->_elementId ) ) );
        return $this;
    }

	/**
	 * Gets element's specified attribute's value
	 * @param String $attributeName
	 * @return String
	 */
	public function getAttribute($attributeName)
	{
		$command = new Commands\Command($this->_driver, 'element_attribute', null , array('element_id' => $this->_elementId, 'attribute_name' => $attributeName));
		$results = $command->execute();
		return $results['value'];		
	}

    /**
     * Gets element's property CSS
     * @param string $propertyName
     * @return String
     */
    public function getCSSProperty ($propertyName) {
        $params  = array('element_id' => $this->getElementId(), 'property_name' => $propertyName);
        $command = new Commands\Command($this->_driver, 'element_property_name', null , $params);
        $results  = $command->execute();
        return $results['value'];
    }

	/**
	 * Gets whether element is selected
	 * @return Boolean
	 */
	public function isSelected() 
	{
		$command = new Commands\Command($this->_driver, 'element_is_selected', null , array('element_id' => $this->_elementId));
		$results = $command->execute();
		return (trim($results['value']) == "1");		 
	}
	
	/**
	 * Gets whether element is displayed
	 * @return Boolean
	 */
	public function isDisplayed() 
	{
		$command = new Commands\Command($this->_driver, 'element_is_displayed', null , array('element_id' => $this->_elementId));
		$results = $command->execute();
		return (trim($results['value']) == "1");		
	}

	/**
	 * Gets whether element is enabled
	 * @return Boolean
	 */
	public function isEnabled() 
	{
		$command = new Commands\Command($this->_driver, 'element_is_enabled', null , array('element_id' => $this->_elementId));
		$results = $command->execute();
		return (trim($results['value']) == "1");		 
	}

    /**
     * Gets an element's size in pixels
     * @return array
     */
    public function getElementSize()
    {
        $command = new Commands\Command($this->_driver, 'element_size', null , array('element_id' => $this->_elementId));
        $results = $command->execute();
        return $results['value'];
    }

	/**
	 * Clear current element's text
	 */
	public function clear() 
	{
		$command = new Commands\Command($this->_driver, 'clear_element', null , array('element_id' => $this->_elementId));
		$command->execute();
	}
	
	/**
	 * Click on element
	 */
	public function click() 
	{
		$command = new Commands\Command($this->_driver, 'click_element', null , array('element_id' => $this->_elementId));
		$command->execute(); 
	}
	
	/**
	 * Submit form from element
	 */
	public function submit() 
	{
		$command = new Commands\Command($this->_driver, 'element_submit', null , array('element_id' => $this->_elementId));
		$command->execute(); 
	}
	
	/**
	 * Gets element's description
	 * @return Array
	 */
	public function describe() 
	{
		$command = new Commands\Command($this->_driver, 'describe_element', null , array('element_id' => $this->_elementId));
		$results = $command->execute();
		return $results['value']; 
	}

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
	public function getCoordinates() 
	{
		$command = new Commands\Command($this->_driver, 'element_location', null , array('element_id' => $this->_elementId));
		$results = $command->execute();		
		return $results['value'];		
	}
	
	/**
	 * Get element's coordinates after scrolling
	 * @return Array
	 */
	public function getLocationOnScreenOnceScrolledIntoView()
	{
		$command = new Commands\Command($this->_driver, 'element_location_view', null , array('element_id' => $this->_elementId));
		$results = $command->execute();
		return $results['value'];		
	}

	/**
	 * Find element within current element
	 * @param By $locator
	 * @param Boolean $polling
	 * @return Nearsoft\SeleniumClient\WebElement
	 */
	public function findElement(By $locator, $polling = false) 
	{
		return $this->_driver->findElement($locator, $polling, $this->_elementId); 
	}
	
	/**
	 * Find elements within current element
	 * @param By $locator
	 * @param Boolean $polling
	 * @return Nearsoft\SeleniumClient\WebElement[]
	 */
	public function findElements(By $locator, $polling = false) 
	{
		return $this->_driver->findElements($locator, $polling, $this->_elementId); 
	}
	
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
	 * @return Nearsoft\SeleniumClient\WebElement
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
	 * @return Nearsoft\SeleniumClient\WebElement
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
	 * @throws Nearsoft\SeleniumClient\Exceptions\WebDriverWaitTimeout
	 * @return Nearsoft\SeleniumClient\WebElement
	 */
	public function waitForElementUntilTextIsChanged($targetText, $timeOutSeconds = 5)
	{
		$wait = true;
		
		while ($wait)
		{
			$currentText = $this->getText();

			if ($currentText == $targetText) { $wait = false; }
			else if ($timeOutSeconds <= 0) { throw new Exceptions\WebDriverWaitTimeout("Timeout for waitForElementUntilTextIsChange." ); }
			
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
	 * @throws Nearsoft\SeleniumClient\Exceptions\WebDriverWaitTimeout
	 * @return Nearsoft\SeleniumClient\WebElement
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
			else if ($attempts <= 0) { throw new Exceptions\WebDriverWaitTimeout("Timeout for waitForElementUntilIsPresentAndTextIsChange." ); }
			
			sleep(1);
			
			$attempts = $attempts - 1;
		}
		return $dynamicElement;
	}
}