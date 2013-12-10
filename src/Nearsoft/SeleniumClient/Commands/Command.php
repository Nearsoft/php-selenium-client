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

namespace Nearsoft\SeleniumClient\Commands;

use Nearsoft\SeleniumClient\Commands\Dictionary;

class Command
{	
	private $_driver;		
	private $_name;
	private $_params;	
	private $_urlParams;	

	private $_url;
	private $_httpMethod;		
	private $_polling;
	private $_response;

	public function __construct($driver, $name, $params = null, $urlParams = null)
	{
		$this->_driver    = $driver;
		$this->_name      = $name;
		$this->_params    = $params;
		$this->_urlParams = $urlParams;

		$this->setUrl();
	  	$this->setHttpMethod();

		return $this;
	}	

	public function getUrl()        { return $this->_url; }	
	public function getParams()     { return $this->_params; }
	public function getHttpMethod() { return $this->_httpMethod; }
	public function getPolling()    { return $this->_polling; }
	public function getResponse()   { return $this->_response; }

	private function setUrl()
	{
		$path = Dictionary::$commands[$this->_name]['path']; 
		$path = str_replace('{session_id}', $this->_driver->getSessionId(), $path);
		
		if($this->_urlParams){
			foreach($this->_urlParams as $param_name => $value) {
				$path = str_replace("{{$param_name}}", $value, $path);
			}
		}

		$this->_url = "{$this->_driver->getHubUrl()}/{$path}";
	} 

	private function setHttpMethod()
	{		
		$this->_httpMethod = Dictionary::$commands[$this->_name]['http_method']; 
	} 

	public function setPolling($value)
	{
		$this->_polling = $value;
	}  

	public function execute($trace = false) 
	{
	  $httpClient = $this->_driver->getHttpClient();

	  $this->_response = $httpClient->execute($this, $trace);
	  
	  return $this->_response['body'];
	}	
}