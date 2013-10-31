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

class TargetLocator
{
	private $_driver;
	
	public function __construct(WebDriver $driver)
	{
		$this->_driver = $driver;
	}

	/**
	 * Change to the Window by passing in the name
	 * @param String $identifier either window name or window handle
	 * @return \SeleniumClient\WebDriver The current webdriver
	 */
	public function window($identifier)
	{
		$params = array ('name' => $identifier);
		$command = new Commands\Window($this->_driver, $params);		
		$command->execute();
		return $this->_driver;
	}

	/**
	 * Focus on specified frame
	 * @param Mixed $identifier. Null will get default frame. String will get by frame name. Integer will get frame by index. WebElement will get by web element relation.
	 * @return \SeleniumClient\WebDriver The current webdriver
	 */
	public function frame($identifier)
	{
		$idParam = null;
		$type = gettype($identifier); 
		if($type == 'string' || $type == 'integer'){
			$idParam = $identifier;
		}
		elseif($type == 'object' && $identifier instanceof WebElement){
			$idParam = array('ELEMENT' => $identifier->getElementId());
		}

		$params = array ('id' => $idParam);
		$command = new Commands\Frame($this->_driver, $params);		
		$command->execute();
		return $this->_driver;
	}

	/**
	 * Finds the active element on the page and returns it
	 * @return WebElement
	 */
	public function activeElement()
	{
		$command = new Commands\ActiveElement($this->_driver);	
		$results = $command->execute();	
		return new WebElement($this->_driver, $results['value']['ELEMENT']);
	}

	/**
	 *  Switches to the currently active modal dialog for this particular driver instance.
	 * @return \SeleniumClient\Alert
	 */
	public function alert()
	{
		return new Alert($this->_driver);
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

        $existingHandles = $this->_driver->getWindowHandles();
        $anchor = new WebElement( $this->_driver, $element['ELEMENT'] );
        $anchor->click();

        $this->_driver->executeScript( 'var d=document,a=arguments[0];a.parentNode.removeChild(a);', array( $element ) );
        $newHandles = array_values( array_diff( $this->_driver->getWindowHandles(), $existingHandles ) );
        $newHandle = $newHandles[0];
        $oldHandle = $this->_driver->getWindowHandle();

        $this->window( $newHandle );

        return $oldHandle;
    }
}