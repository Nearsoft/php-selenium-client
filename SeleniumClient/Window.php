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

class Window
{
	private $_driver   = null;
	
	public function __construct(WebDriver $driver)
	{
		$this->_driver = $driver;
	}	

    /**
     * Maximizes current Window
     */
    public function maximize() {
     	$commannd = new Commands\WindowMaximize($this->_driver, null, array('window_handle' => 'current'));
        $commannd->execute();
    }
    
	/**
	 * Sets current window size
	 * @param Integer $width
	 * @param Integer $height
	 */
	public function setSize($width, $height)
	{
		$params = array ('width' => $width, 'height' => $height);
		$command = new Commands\SetWindowSize($this->_driver, $params, array('window_handle' => 'current'));			
		$command->execute();
	}
	
	/**
	 * Gets current window's size
	 * @return Array
	 */
	public function getSize()
	{
		$command = new Commands\GetWindowSize($this->_driver, null,  array('window_handle' => 'current'));			
		$results = $command->execute();
		return $results['value'];
	}
	
	/**
	 * Sets current window's position
	 * @param Integer $x
	 * @param Integer $y
	 */
	public function setPosition($x, $y)
	{
		$params = array ('x' => $x, 'y' => $y);
		$command = new Commands\SetWindowPosition($this->_driver, $params,  array('window_handle' => 'current'));			
		$command->execute();
	}
	
	/**
	 * Gets current window's position
	 * @return Array
	 */
	public function getPosition()
	{
		$command = new Commands\GetWindowPosition($this->_driver, null, array('window_handle' => 'current'));
		$results = $command->execute(); 
		return $results['value'];	
	}
}