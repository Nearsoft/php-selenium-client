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

class Timeouts
{
	private $_driver   = null;
	
	public function __construct(WebDriver $driver)
	{
		$this->_driver = $driver;
	}

    /**
     * Sets default time for selenium to wait for an element to be present
     * @param Integer $milliseconds
     */
    public function implicitWait($milliseconds)
    {
        $params = array ('ms' => $milliseconds );
        $command = new Commands\Command($this->_driver, 'implicit_wait', $params);
        $command->execute();
    }

    /**
     * Sets page_load timeout
     * @param int $milliseconds
     */
    public function pageLoadTimeout($milliseconds)
    {
        $params = array ('type' => 'page load','ms' => $milliseconds );
        $command = new Commands\Command($this->_driver, 'load_timeout', $params);
        $command->execute();
    }

    /**
     * Set's Async Script timeout
     * @param Integer $milliseconds
     */
    public function setScriptTimeout($milliseconds)
    {
        $params = array('ms' => $milliseconds);
        $command = new Commands\Command($this->_driver, 'async_script_timeout', $params);
        $command->execute();
    }
}