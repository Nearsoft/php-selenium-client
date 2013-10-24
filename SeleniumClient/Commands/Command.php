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

namespace SeleniumClient\Commands;

use SeleniumClient\Http\HttpClient;

abstract class Command
{	
	protected $_driver;	
	protected $_path;
	protected $_httpMethod;	
	protected $_params;	
	protected $_urlParams;	
	protected $_polling;
	protected $_response;	

	public function __construct($driver, $params = null, $urlParams = null)
	{
		$this->_driver    = $driver;
		$this->_params    = $params;
		$this->_urlParams = $urlParams;
		$this->setUp();	
		return $this;
	}	

	abstract protected function setUp();

	protected function setPost()    {$this->_httpMethod = HttpClient::POST; }
	protected function setGet()     {$this->_httpMethod = HttpClient::GET; }
	protected function setDelete()  {$this->_httpMethod = HttpClient::DELETE; }

	public function getUrl()        { return "{$this->_driver->getHubUrl()}/{$this->_path}"; }
	public function getParams()     { return $this->_params; }
	public function getHttpMethod() { return $this->_httpMethod; }
	public function getPolling()    { return $this->_polling; }
	public function getResponse()   { return $this->_response; }

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