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

class Navigation
{
    private $_driver = null;

    public function __construct(WebDriver $driver)
    {
        $this->_driver = $driver;
    }

    /**
    * Navigate back in history
    */
    public function back()
    {
        $command = new Commands\Back($this->_driver);
        $command->execute();
    }

    /**
     * Navigate forward in history
     */
    public function forward()
    {
        $command = new Commands\Forward($this->_driver);
        $command->execute();
    }

    /**
     * Refreshes current page
     */
    public function refresh()
    {
        $command = new Commands\Refresh($this->_driver);
        $command->execute();
    }

    /**
     * Navigates to specified url
     * @param String $url
     */
    public function to($url)
    {
        $params  = array ('url' => $url);
        $command = new Commands\GetUrl($this->_driver, $params);
        $command->execute();
    }


}