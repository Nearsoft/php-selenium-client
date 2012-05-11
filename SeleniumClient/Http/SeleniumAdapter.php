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

require_once "Exceptions.php";

class SeleniumAdapter extends HttpClient
{
	public function execute()
	{
		parent::execute();
		
		$this->validateSeleniumResponseCode();
		$this->validateHttpCode ();
		
		return $this->_responseBody;
	}
	
	protected function validateHttpCode()
	{
		// Http response exceptions
		switch (intval(trim($this->_responseHeaders['http_code'])))
		{
			case 400:
				throw new SeleniumMissingCommandParametersException((string) $this->_responseHeaders['http_code'], $this->_responseHeaders['url']);
				break;
			case 405:
				throw new SeleniumInvalidCommandMethodException((string) $this->_responseHeaders['http_code'], $this->_responseHeaders['url']);
				break;
			case 500:
				if (!$this->_polling) { throw new SeleniumFailedCommandException((string) $this->_responseHeaders['http_code'], $this->_responseHeaders['url']); }
				break;
			case 501:
				throw new SeleniumUnimplementedCommandException((string) $this->_responseHeaders['http_code'], $this->_responseHeaders['url']);
				break;
			default:
				// Looks for 4xx http codes
				if (preg_match("/^4[0-9][0-9]$/", $this->_responseHeaders['http_code'])) { throw new SeleniumInvalidRequestException((string) $this->_responseHeaders['http_code'], $this->_responseHeaders['url']); }
				break;
		}
	}
	
	protected function validateSeleniumResponseCode()
	{
		// Selenium response status exceptions
		if ($this->_responseBody != null)
		{
			switch (intval($this->_responseBody["status"]))
			{
				case 7:
					if (!$this->_polling) { throw new SeleniumNoSuchElementException(); }
					break;
				case 8:
					throw new SeleniumNoSuchFrameException();
					break;
				case 9:
					throw new SeleniumUnknownCommandException();
					break;
				case 10:
					throw new SeleniumStaleElementReferenceException();
					break;
				case 11:
					throw new SeleniumElementNotVisibleException();
					break;
				case 12:
					throw new SeleniumInvalidElementStateException();
					break;
				case 13:
					throw new SeleniumUnknownErrorException();
					break;
				case 15:
					throw new SeleniumElementIsNotSelectableException();
					break;
				case 17:
					throw new SeleniumJavaScriptErrorException();
					break;
				case 19:
					throw new SeleniumXPathLookupErrorException();
					break;
				case 21:
					throw new SeleniumTimeoutException();
					break;
				case 23:
					throw new SeleniumNoSuchWindowException();
					break;
				case 24:
					throw new SeleniumInvalidCookieDomainException();
					break;
				case 25:
					throw new SeleniumUnableToSetCookieException();
					break;
				case 26:
					throw new SeleniumUnexpectedAlertOpenException();
					break;
				case 27:
					throw new SeleniumNoAlertOpenErrorException();
					break;
				case 28:
					throw new SeleniumScriptTimeoutException();
					break;
				case 29:
					throw new SeleniumInvalidElementCoordinatesException();
					break;
				case 30:
					throw new SeleniumIMENotAvailableException();
					break;
				case 31:
					throw new SeleniumIMEEngineActivationFailedException();
					break;
				case 32:
					throw new SeleniumInvalidSelectorException();
					break;
			}
		}
	}
}