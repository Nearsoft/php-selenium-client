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

class Options
{
	private $_driver   = null;
	private $_window   = null;
	private $_timeouts = null;
	
	public function __construct(WebDriver $driver)
	{
		$this->_driver = $driver;
	}	

	/**
	 * Gets Timeouts object
	 * @return SeleniumClient\Timeouts
	 */
	public function timeouts()
	{
		if(!$this->_timeouts)
		{
			$this->_timeouts = new Timeouts($this->_driver);
		}
		return $this->_timeouts;
	}

	/**
	 * Gets Window object
	 * @return SeleniumClient\Window
	 */
	public function window()
	{
		if(!$this->_window)
		{
			$this->_window = new Window($this->_driver);
		}
		return $this->_window;
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
	public function addCookie($name, $value, $path = null, $domain = null, $secure = null, $expiry = null)
	{
		$cookie = new Cookie($name, $value, $path, $domain, $secure, $expiry);
		$params = array ('cookie' => $cookie->getArray());
		$command = new Commands\AddCookie($this->_driver, $params);
		$command->execute(); 		
	}
	
	/**
	 * Gets current cookies
	 * @return Array
	 */
	public function getCookies()
	{
		$command = new Commands\GetCookies($this->_driver);
		$results = $command->execute(); 	
		return Cookie::buildFromArray($results['value']);		
	}

	/**
	 * Gets a cookie by name
	 * @return Array
	 */
	public function getCookieNamed($cookieName)
	{
		$cookies = $this->getCookies();
		$matches = array_filter($cookies,function($cookie) use ($cookieName){ return $cookieName == $cookie->getName();});
		if(count($matches) > 1){ throw new \Exception("For some reason there are more than 1 cookie named {$cookieName}");}
		$matches = array_values($matches);
		return count($matches) > 0 ? $matches[0] : null;
	}

	/**
	 * Remove a cookie
	 * @param SeleniumClient\Cookie $cookie
	 */
	public function deleteCookie($cookie)
	{
		$this->deleteCookieNamed($cookie->getName());			
	}
	
	/**
	 * Remove a cookie
	 * @param String $cookieName
	 */
	public function deleteCookieNamed($cookieName)
	{
		$command = new Commands\ClearCookie($this->_driver, null, array('cookie_name' => $cookieName));
		$command->execute(); 			
	}
	
	/**
	 * Removes all current cookies
	 */
	public function deleteAllCookies()
	{
		$command = new Commands\ClearCookies($this->_driver);
		$command->execute(); 	
	}
}