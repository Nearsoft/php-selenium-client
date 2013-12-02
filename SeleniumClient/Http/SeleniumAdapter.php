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

namespace SeleniumClient\Http;

use SeleniumClient\Commands;
use SeleniumClient\Exceptions;

class SeleniumAdapter extends HttpClient
{
	public function execute(\SeleniumClient\Commands\Command $command)
	{
		$response = parent::execute($command);
		$this->validateSeleniumResponseCode($response,$command->getPolling());
		$this->validateHttpCode($response,$command->getPolling());	
		return $response;
	}
	
	protected function validateHttpCode($response, $polling)
	{
		// Http response exceptions
		switch (intval(trim($response['headers']['http_code'])))
		{
			case 400:
				throw new MissingCommandParameters((string) $response['headers']['http_code'], $response['headers']['url']);
				break;
			case 405:
				throw new InvalidCommandMethod((string) $response['headers']['http_code'], $response['headers']['url']);
				break;
			case 500:
				if (!$polling) { throw new FailedCommand((string) $response['headers']['http_code'], $response['headers']['url']); }
				break;
			case 501:
				throw new UnimplementedCommand((string) $response['headers']['http_code'], $response['headers']['url']);
				break;
			default:
				// Looks for 4xx http codes
				if (preg_match("/^4[0-9][0-9]$/", $response['headers']['http_code'])) { throw new InvalidRequest((string) $response['headers']['http_code'], $response['headers']['url']); }
				break;
		}
	}
	
	protected function validateSeleniumResponseCode($response, $polling)
	{
		// Selenium response status exceptions
		if ($response['body'] != null)
		{
			if (isset($response['body']["value"]["localizedMessage"]))
			{
				$message = $response['body']["value"]["localizedMessage"];
			}
			else
			{
				$message = "";
			}
			switch (intval($response['body']["status"]))
			{
				case 7:
					if (!$polling) {throw new NoSuchElement($message);}
					break;
				case 8:
					throw new NoSuchFrame($message);
					break;
				case 9:
					throw new UnknownCommand($message);
					break;
				case 10:
					throw new StaleElementReference($message);
					break;
				case 11:
					throw new ElementNotVisible($message);
					break;
				case 12:
					throw new InvalidElementState($message);
					break;
				case 13:
					throw new UnknownError($message);
					break;
				case 15:
					throw new ElementIsNotSelectable($message);
					break;
				case 17:
					throw new JavaScriptError($message);
					break;
				case 19:
					throw new XPathLookupError($message);
					break;
				case 21:
					throw new Timeout($message);
					break;
				case 23:
					throw new NoSuchWindow($message);
					break;
				case 24:
					throw new InvalidCookieDomain($message);
					break;
				case 25:
					throw new UnableToSetCookie($message);
					break;
				case 26:
					throw new UnexpectedAlertOpen($message);
					break;
				case 27:
					throw new NoAlertOpenError($message);
					break;
				case 28:
					throw new ScriptTimeout($message);
					break;
				case 29:
					throw new InvalidElementCoordinates($message);
					break;
				case 30:
					throw new IMENotAvailable($message);
					break;
				case 31:
					throw new IMEEngineActivationFailed($message);
					break;
				case 32:
					throw new InvalidSelector($message);
					break;
			}
		}
	}
}