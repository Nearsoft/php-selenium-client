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

        $this->assertEquals($url, $this->_driver->getCurrentUrl());
    }

    public function testNavigationMagicBackShouldCallMethodBack()
    {
        $this->_driver->navigationTo('google.com');
        $result = $this->_driver->navigationBack();
        $this->assertNull($result);
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

    public function testMagicNavigationBackShouldCallMethodBack()
    {
        $mock = $this->getMock('Navigation', array('back'));

        $mock->expects($this->exactly(1))
            ->method('back');

        $this->_driver->setNavigate($mock);

        $this->_driver->navigationBack();
    }

    public function testMagicNavigationForwardShouldCallMethodForward()
    {
        $mock = $this->getMock('Navigation', array('forward'));

        $mock->expects($this->exactly(1))
             ->method('forward');

        $this->_driver->setNavigate($mock);

        $this->_driver->navigationForward();
    }

    public function testMagicNavigationRefreshShouldCallMethodRefresh()
    {
        $mock = $this->getMock('Navigation', array('refresh'));

        $mock->expects($this->exactly(1))
            ->method('refresh');

        $this->_driver->setNavigate($mock);

        $this->_driver->navigationRefresh();
    }

    public function testMagicNavigationToShouldCallMethodTo()
    {
        $mock = $this->getMock('Navigation', array('to'));

        $mock->expects($this->exactly(1))
            ->method('to')
            ->with($this->equalTo('google.com'));

        $this->_driver->setNavigate($mock);

        $this->_driver->navigationTo('google.com');
    }

}
