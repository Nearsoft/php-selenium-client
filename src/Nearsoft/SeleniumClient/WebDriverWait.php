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

class WebDriverWait
{
	private $_seconds;
	
	function __construct($seconds = 1)
	{
		if (!is_numeric($seconds)) { throw new \Exception("Must specify number"); }
		
		$this->_seconds = $seconds;
	}
	
	public function getSeconds()
	{
		return $this->_seconds;
	}
	
	/**
	 * Stop current flow until specified condition is completed
	 * @param SeleniumClient\WebDriver / SeleniumClient\WebElement $seleniumObject
	 * @param String $method
	 * @param Array $args
	 * @throws \Exception
	 * @throws Nearsoft\SeleniumClient\Exceptions\WebDriverWaitTimeout
	 * @return mixed
	 */
	public function until($seleniumObject, $method, array $args)
	{
        if (!isset($seleniumObject)) {
            throw new \Exception("seleniumObject parameter has not been initialized");
        } else if (!isset($method)) {
            throw new \Exception("method parameter has not been initialized");
        }

        $seconds = $this->_seconds;
        $elapsed = -1;
        $start = microtime(true);

        while ($elapsed < $seconds) {
            // the amount of time to sleep until the next whole second
            if ($elapsed > 0 && ($sleep = 1000000 * (1 - ($elapsed - (int)$elapsed))) > 0) {
                usleep($sleep);
            }

			try {
				$resultObject = call_user_func_array(array ($seleniumObject, $method), $args);
                if ($resultObject !== null && $resultObject !== false) {
                    return $resultObject;
                }
			} catch (\Exception $ex) {
			}

            $elapsed = microtime(true) - $start;
        }

        $exMessage = "Timeout for specified condition caused by object of class: " . get_class($seleniumObject) . ", method invoked: " . $method . ".";

        if ($args != null && count($args) > 0) {
            $stringArgs = Array();
            foreach ($args as $arg) {
                if (is_object($arg) && method_exists($arg, '__toString')) {
                    $stringArgs[] = $arg;
                } else if (is_object($arg) && !method_exists($arg, '__toString')) {
                    $stringArgs[] = get_class($arg);
                } else {
                    $stringArgs[] = $arg;
                }
            }

            $exMessage .= " Arguments: <" . implode(">,<", $stringArgs) . ">";
        }

        throw new Exceptions\WebDriverWaitTimeout($exMessage);
	}
}