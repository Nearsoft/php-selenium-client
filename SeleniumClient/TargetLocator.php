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

use SeleniumClient\Http\SeleniumJavaScriptErrorException;

require_once __DIR__ . '/WebDriver.php';

class TargetLocator
{
	private $_driver;
	
	public function __construct(WebDriver $driver)
	{
		$this->_driver = $driver;
	}
	
	#region TargetLocator members
	/**
	 * Move to a different frame using its index
	 * @param Integer $frameIndex
	 * @return \SeleniumClient\WebDriver The current webdriver
	 */
	public function getFrameByIndex($frameIndex)
	{
		
		$this->_driver->getFrame($frameIndex);

		return $this->_driver;
	}

	/**
	 * Move to different frame using its name
	 * @param String $frameName
	 * @return \SeleniumClient\WebDriver The current webdriver
	 */
	public function getFrameByName($frameName)
	{
		//We should validate that frameName is string
		/*
		if ($frameName == null)
		{
			throw new ArgumentNullException("frameName", "Frame name cannot be null");
		}
		*/

		$this->_driver->getFrame($frameName);

		return $this->_driver;
	}

	/**
	 * Move to a frame element.
	 * @param WebElement $frameElement
	 * @return \SeleniumClient\WebDriver The current webdriver
	 */
	public function getFrameByWebElement(WebElement $frameElement)
	{
		//We should validate that frameElement is string
		/*
		if (frameElement == null)
		{
			throw new ArgumentNullException("frameElement", "Frame element cannot be null");
		}

		RemoteWebElement convertedElement = frameElement as RemoteWebElement;
		if (convertedElement == null)
		{
			throw new ArgumentException("frameElement cannot be converted to RemoteWebElement", "frameElement");
		}
		*/

		$frameId = $frameElement->getElementId();
		$target = array('ELEMENT' => $frameId);
		$this->_driver->getFrame($target);

		return $this->_driver;
	}

	/**
	 * Change to the Window by passing in the name
	 * @param String $windowName
	 * @return \SeleniumClient\WebDriver The current webdriver
	 */
	public function getWindow($windowName)
	{
		$this->_driver->getWindow($windowName);
		
		return $this->_driver;
	}

	/**
	 * Change the active frame to the default
	 * @return \SeleniumClient\WebDriver The current webdriver
	 */
	public function getDefaultFrame()
	{
		$this->_driver->getFrame(null);

		return $this->_driver;
	}

	/**
	 * Finds the active element on the page and returns it
	 * @return WebElement
	 */
	public function getActiveElement()
	{
		$webElement = null;

		$webElement = $this->_driver->getActiveElement();

		return $webElement;
	}


	/**
	 *  Switches to the currently active modal dialog for this particular driver instance.
	 * @return \SeleniumClient\Alert
	 */
	public function getAlert()
	{
		// N.B. We only execute the GetAlertText command to be able to throw
		// a NoAlertPresentException if there is no alert found.
		//$this->_driver->getAlertText();
		return new Alert($this->_driver); //validate that the Alert object can be created, if not throw an exception, try to use a factory singleton o depency of injection to only use 1 instance
	}

    /**
     * Opens a new tab for the given URL
     * @param string $url The URL to open
     * @return string The handle of the previously active window
     * @see http://stackoverflow.com/a/9122450/650329
     * @throws SeleniumJavaScriptErrorException If unable to open tab
     */
    public function newTab($url)
    {
        $script = "var d=document,a=d.createElement('a');a.target='_blank';a.href='%s';a.innerHTML='.';d.body.appendChild(a);return a";
        $element = $this->_driver->executeScript( sprintf( $script, $url ) );

        if ( empty( $element ) ) {
            throw new SeleniumJavaScriptErrorException( 'Unable to open tab' );
        }

        $existingHandles = $this->_driver->getCurrentWindowHandles();
        $anchor = new WebElement( $this->_driver, $element['ELEMENT'] );
        $anchor->click();

        $this->_driver->executeScript( 'var d=document,a=arguments[0];a.parentNode.removeChild(a);', array( $element ) );
        $newHandles = array_values( array_diff( $this->_driver->getCurrentWindowHandles(), $existingHandles ) );
        $newHandle = $newHandles[0];
        $oldHandle = $this->_driver->getCurrentWindowHandle();

        $this->getWindow( $newHandle );

        return $oldHandle;
    }
	#endregion
}