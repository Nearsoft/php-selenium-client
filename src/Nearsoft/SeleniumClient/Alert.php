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

class Alert
{

	private $_driver;

	/**
	 * @param WebDriver $driver
	 */
	public function __construct(WebDriver $driver) { $this->_driver = $driver; }

	/**
	 * Gets the text of the alert.
	 * @return String
	 */
	public function getText() 
	{ 
		$command = new Commands\Command($this->_driver, 'get_alert_text');
		$results = $command->execute(); 	
		return $results['value'];
	}

	/**
	 * Dismisses the alert.
	 */
	public function dismiss() 
	{ 
		$command = new Commands\Command($this->_driver, 'dismiss_alert');
		$command->execute(); 	
	}

	/**
	 * Accepts the alert.
	 */
	public function accept() 
	{ 
		$command = new Commands\Command($this->_driver, 'accept_alert');
		$command->execute(); 	
	}

	/**
	 * Sends keys to the alert.
	 * @param String $string
	 */
	public function sendKeys($string)
	{
		if(is_string($string)){
			$params = array ('text' => $string);
			$command = new Commands\Command($this->_driver, 'set_alert_text', $params);
			$command->execute();
		}
		else{
			throw new \Exception("Value must be a string");
		}
	}
}