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

namespace Nearsoft\SeleniumClient\Http;

use Nearsoft\SeleniumClient\Commands as Commands;

class HttpClient
{
	const POST   = "POST";
	const GET    = "GET";
	const DELETE = "DELETE";
	
	protected $_traceAll = false;	
	
	public function getTraceAll() { return $this->_traceAll; }
	
	public function setTraceAll($value)
	{
		$this->_traceAll = $value;	
		return $this;
	}

    /**
     * @return string The response body
     * @throws \Exception
     */
    public function execute(Commands\Command $command, $trace = false)
	{
		if ($command->getUrl() == "" || $command->getHttpMethod() == "") { throw new \Exception("Must specify URL and HTTP METHOD"); }
		
		$curl = curl_init($command->getUrl());
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=UTF-8','Accept: application/json'));
		
		if($command->getHttpMethod() == HttpClient::POST)
		{
			curl_setopt($curl, CURLOPT_POST, true);
				
			if ($command->getParams() && is_array($command->getParams())) 
			{ 
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($command->getParams())); 
			}
            else 
            { 
            	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Length: 0')); 
            }
		}
		else if ($command->getHttpMethod() == HttpClient::DELETE) { curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE'); }
		else if ($command->getHttpMethod() == HttpClient::GET) { /*NO ACTION NECESSARY*/ }
		
		$rawResponse = trim(curl_exec($curl));
		
		$responseBody = json_decode($rawResponse, true);
		
		$responseHeaders=curl_getinfo($curl);

		if ($this->_traceAll || $trace)
		{
			echo "\n***********************************************************************\n";
			echo "URL: " . $command->getUrl() . "\n";
			echo "METHOD: " . $command->getHttpMethod() . "\n";
			
			echo "PARAMETERS: ";
			if (is_array($command->getParams())) { echo print_r($command->getParams()); }
			else echo "NONE"; { echo "\n"; }
			
			echo "RESULTS:" .  print_r($responseBody);
			echo "\n";
			echo "CURL INFO: ";
			echo print_r($responseHeaders);
			
			echo "\n***********************************************************************\n";
		}
		
		curl_close($curl);

		$response = array(
		    "headers" => $responseHeaders,
		    "body"    => $responseBody,
		);

        return $response;
	}
}