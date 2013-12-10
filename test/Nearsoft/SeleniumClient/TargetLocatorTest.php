<?php

require_once __DIR__ . '/AbstractTest.php';

use Nearsoft\SeleniumClient\Alert;
use Nearsoft\SeleniumClient\By;
use Nearsoft\SeleniumClient\TargetLocator;


class TargetLocatorTest extends AbstractTest
{
    private $_targetLocator = null;
	
	public function setUp()
	{
        parent::setUp();
		$this->_targetLocator = new TargetLocator($this->_driver);
	}

	public function testWindowShouldGetWindowWebElement()
	{
		$this->_driver->findElement(By::id("btnPopUp1"))->click();
		$webElement = $this->_targetLocator->window("popup1")->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window");
		$this->assertEquals("test window", $webElement->getAttribute("value"));
	}
	
	public function testWindowShouldGetWindowWebElementGetBackToParentWindow()
	{
		$window1Handle = $this->_driver->getWindowHandle();

		$this->_driver->findElement(By::id("btnPopUp1"))->click();
		$this->_targetLocator->window($window1Handle);
		$this->_driver->findElement(By::id("btnPopUp2"))->click();

		$webElement = $this->_targetLocator->window("popup1")->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window 1");
		
		$this->assertEquals("test window 1", $webElement->getAttribute("value"));

		$webElement = $this->_targetLocator->window("popup2")->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window 2");
		
		$this->assertEquals("test window 2", $webElement->getAttribute("value"));

		$webElement = $this->_targetLocator->window($window1Handle)->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window default");

		$this->assertEquals("test window default", $webElement->getAttribute("value"));

		$this->_targetLocator->window("popup1");
		$this->_driver->close();

		$this->_targetLocator->window("popup2");
		$this->_driver->close();
		
		$this->_targetLocator->window($window1Handle);
	}

	public function testFrameShouldGetDefaultframe()
	{
		$webElement = $this->_targetLocator->frame(null)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");
		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}

	public function testFrameShouldGetFrameByIndex()
	{
		$webElement = $this->_targetLocator->frame(0)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));

		$webElement = $this->_targetLocator->frame(null)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe default");

		$this->assertEquals("test iframe default", $webElement->getAttribute("value"));

		$webElement = $this->_targetLocator->frame(1)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe1");
		
		$this->assertEquals("test iframe1", $webElement->getAttribute("value"));
	}

	public function testFrameShouldGetFrameByName()
	{
		$webElement = $this->_targetLocator->frame("iframe1")->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");
		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}
	
	public function testFrameShouldGetFrameByNameShouldGetFrameWebElementGetBackToParentWindow()
	{
		$webElement = $this->_targetLocator->frame("iframe1")->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));

		$webElement = $this->_targetLocator->frame(null)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe default");

		$this->assertEquals("test iframe default", $webElement->getAttribute("value"));

		$webElement = $this->_targetLocator->frame("iframe2")->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe2");

		$this->assertEquals("test iframe2", $webElement->getAttribute("value"));
	}
	
	public function testFrameShouldGetFrameByWebElementShouldGetFrameWebElement()
	{
		$webElement = $this->_driver->findElement(By::id("iframe1"));
		$webElement = $this->_targetLocator->frame($webElement)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");
		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}

	public function testFrameShouldGetFrameByWebElementShouldGetFrameWebElementGetBackToParentWindow()
	{
		$webElement = $this->_driver->findElement(By::id("iframe1"));
		$webElement = $this->_targetLocator->frame($webElement)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));

		$webElement = $this->_targetLocator->frame(null)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe default");

		$this->assertEquals("test iframe default", $webElement->getAttribute("value"));

		$webElement = $this->_driver->findElement(By::id("iframe2"));
		$webElement = $this->_targetLocator->frame($webElement)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe2");

		$this->assertEquals("test iframe2", $webElement->getAttribute("value"));
	}

	public function testActiveElementShouldGetActiveElement()
	{
		$this->_driver->findElement(By::id("txt1"))->sendKeys("test");		
		$webElement = $this->_targetLocator->activeElement();
		$this->assertInstanceOf('Nearsoft\SeleniumClient\WebElement', $webElement);
		$this->assertEquals("test", $webElement->getAttribute("value"));
	}

	public function testAlertShouldGetAlertInstance()
	{
		$this->_driver->findElement(By::id("btnAlert"))->click();
		$alert = $this->_targetLocator->alert();		
		$this->assertTrue($alert instanceof Alert);
	}	

    public function testNewTabShouldGetNewWindow()
    {
        $oldHandle1 = $this->_driver->getWindowHandle();
        $numHandles = count($this->_driver->getWindowHandles());
        $oldHandle2 = $this->_targetLocator->newTab($this->_url);
        $this->assertEquals($oldHandle1, $oldHandle2);
        $newHandle = $this->_driver->getWindowHandle();
        $this->assertNotEquals($oldHandle1, $newHandle);
        $this->assertEquals($numHandles + 1, count($this->_driver->getWindowHandles()));
    }	
}