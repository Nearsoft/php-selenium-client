<?php

require_once __DIR__ . '/AbstractTest.php';

use Nearsoft\SeleniumClient\By;

class TimeoutsTest extends AbstractTest
{
    public function testImplicitWait()
    {
        $this->_driver->findElement(By::id("btnAppendDiv"))->click();

        $timeouts = $this->_driver->manage()->timeouts();

        $timeouts->implicitWait(5000);

        $webElement = $this->_driver->findElement(By::id("dDiv1-0")); // This takes 5 seconds to be present

        $this->assertInstanceOf('Nearsoft\SeleniumClient\WebElement', $webElement);
    }

    public function testPageLoadTimeout()
    {
        $timeOuts = $this->_driver->manage()->timeouts();

        $timeOuts->pageLoadTimeout(1);

        $this->setExpectedException('Nearsoft\SeleniumClient\Exceptions\ScriptTimeout');

        $this->_driver->get($this->_url."/formReceptor.php");
    }

    public function testSetScriptTimeout()
    {
        $timeouts = $this->_driver->manage()->timeouts();

        $timeouts->setScriptTimeout(1);

        $this->setExpectedException('Nearsoft\SeleniumClient\Exceptions\ScriptTimeout');

        $this->_driver->executeAsyncScript("setTimeout('arguments[0]()',5000);");
    }
}
