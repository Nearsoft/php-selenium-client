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
use Nearsoft\SeleniumClient\Exceptions as SeleniumExceptions;
use Nearsoft\SeleniumClient\Http\Exceptions as HttpExceptions;

class SeleniumAdapter extends HttpClient
{
	public function execute(Commands\Command  $command, $trace = false)
	{
		$response = parent::execute($command, $trace);
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
				throw new HttpExceptions\MissingCommandParameters((string) $response['headers']['http_code'], $response['headers']['url']);
				break;
			case 405:
				throw new HttpExceptions\InvalidCommandMethod((string) $response['headers']['http_code'], $response['headers']['url']);
				break;
			case 500:
				if (!$polling) { throw new HttpExceptions\FailedCommand((string) $response['headers']['http_code'], $response['headers']['url']); }
				break;
			case 501:
				throw new HttpExceptions\UnimplementedCommand((string) $response['headers']['http_code'], $response['headers']['url']);
				break;
			default:
				// Looks for 4xx http codes
				if (preg_match("/^4[0-9][0-9]$/", $response['headers']['http_code'])) { throw new HttpExceptions\InvalidRequest((string) $response['headers']['http_code'], $response['headers']['url']); }
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
					if (!$polling) {throw new SeleniumExceptions\NoSuchElement($message);}
					break;
				case 8:
					throw new SeleniumExceptions\NoSuchFrame($message);
					break;
				case 9:
					throw new SeleniumExceptions\UnknownCommand($message);
					break;
				case 10:
					throw new SeleniumExceptions\StaleElementReference($message);
					break;
				case 11:
					throw new SeleniumExceptions\ElementNotVisible($message);
					break;
				case 12:
					throw new SeleniumExceptions\InvalidElementState($message);
					break;
				case 13:
					throw new SeleniumExceptions\UnknownError($message);
					break;
				case 15:
					throw new SeleniumExceptions\ElementIsNotSelectable($message);
					break;
				case 17:
					throw new SeleniumExceptions\JavaScriptError($message);
					break;
				case 19:
					throw new SeleniumExceptions\XPathLookupError($message);
					break;
				case 21:
					throw new SeleniumExceptions\Timeout($message);
					break;
				case 23:
					throw new SeleniumExceptions\NoSuchWindow($message);
					break;
				case 24:
					throw new SeleniumExceptions\InvalidCookieDomain($message);
					break;
				case 25:
					throw new SeleniumExceptions\UnableToSetCookie($message);
					break;
				case 26:
					throw new SeleniumExceptions\UnexpectedAlertOpen($message);
					break;
				case 27:
					throw new SeleniumExceptions\NoAlertOpenError($message);
					break;
				case 28:
					throw new SeleniumExceptions\ScriptTimeout($message);
					break;
				case 29:
					throw new SeleniumExceptions\InvalidElementCoordinates($message);
					break;
				case 30:
					throw new SeleniumExceptions\IMENotAvailable($message);
					break;
				case 31:
					throw new SeleniumExceptions\IMEEngineActivationFailed($message);
					break;
				case 32:
					throw new SeleniumExceptions\InvalidSelector($message);
					break;
			}
		}
	}
}