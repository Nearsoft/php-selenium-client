<?php

require_once __DIR__ . '/AbstractTest.php';

use SeleniumClient\By;

class NavigationTest extends AbstractTest
{
    public function testToShouldNavigateToUrl()
    {
        $url = $this->_url."/formReceptor.php";

        $navigation =$this->_driver->navigate();

        $navigation->to($url);

        $this->assertEquals($url, $this->_driver->getCurrentPageUrl());
    }

    public function testBackShouldBackBrowserHistory()
    {
        $expectedTitle = $this->_driver->title();

        $navigation =$this->_driver->navigate();

        $navigation->to("http://nearsoft.com");

        $navigation->back();

        $this->assertEquals($expectedTitle, $this->_driver->title());
    }

    public function testRefreshShouldRefreshPageAndEmptyElement()
    {
        $webElement = $this->_driver->findElement(By::id("txt1"));

        $webElement->sendKeys("9999");

        $navigation = $this->_driver->navigate();

        $navigation->refresh();

        $webElement = $this->_driver->findElement(By::id("txt1"));

        $this->assertEquals("", $webElement->getAttribute("value"));
    }

    public function testForwardShouldGoForwardBrowserHistory()
    {
        $navigation =$this->_driver->navigate();

        $navigation->to($this->_url."/formReceptor.php");

        $expectedTitle = $this->_driver->title();

        $navigation->back();

        $navigation->forward();

        $this->assertEquals($expectedTitle, $this->_driver->title());
    }

}
