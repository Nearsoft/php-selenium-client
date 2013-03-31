<?php

require_once __DIR__ . '/AbstractTest.php';

use SeleniumClient\Alert;
use SeleniumClient\By;
use SeleniumClient\TargetLocator;


class TargetLocatorTest extends AbstractTest
{
	/** @var \SeleniumClient\TargetLocator */
    private $_targetLocator = null;
	
	public function setUp()
	{
        parent::setUp();
		$this->_targetLocator = new TargetLocator($this->_driver);
	}
	
	/*
	 * TODO:
	 * consider no selenium server running
	 * fail case for non existing url
	 * test find element by every location method xpath id css	 *
	 */
	
	public function testGetDefaultFrameShouldGetFrameWebElement()
	{
		$webElement = $this->_targetLocator->getDefaultFrame()->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}
	
	public function testGetFrameByIndexShouldGetFrameWebElement()
	{
		$webElement = $this->_targetLocator->getFrameByIndex(0)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}

	public function testGetFrameByIndexShouldGetFrameWebElementGetBackToParentWindow()
	{
		$webElement = $this->_targetLocator->getFrameByIndex(0)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));

		$webElement = $this->_targetLocator->getDefaultFrame()->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe default");

		$this->assertEquals("test iframe default", $webElement->getAttribute("value"));

		$webElement = $this->_targetLocator->getFrameByIndex(1)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe1");
		
		$this->assertEquals("test iframe1", $webElement->getAttribute("value"));
	}

	public function testGetFrameByNameShouldGetFrameWebElement()
	{
		$webElement = $this->_targetLocator->getFrameByName("iframe1")->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}
	
	public function testGetFrameByNameShouldGetFrameWebElementGetBackToParentWindow()
	{
		$webElement = $this->_targetLocator->getFrameByName("iframe1")->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));

		$webElement = $this->_targetLocator->getDefaultFrame()->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe default");

		$this->assertEquals("test iframe default", $webElement->getAttribute("value"));

		$webElement = $this->_targetLocator->getFrameByName("iframe2")->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe2");

		$this->assertEquals("test iframe2", $webElement->getAttribute("value"));
	}
	
	public function testGetFrameByWebElementShouldGetFrameWebElement()
	{
		$webElement = $this->_driver->findElement(By::id("iframe1"));
		$webElement = $this->_targetLocator->getFrameByWebElement($webElement)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}

	public function testGetFrameByWebElementShouldGetFrameWebElementGetBackToParentWindow()
	{
		$webElement = $this->_driver->findElement(By::id("iframe1"));
		$webElement = $this->_targetLocator->getFrameByWebElement($webElement)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));

		$webElement = $this->_targetLocator->getDefaultFrame()->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe default");

		$this->assertEquals("test iframe default", $webElement->getAttribute("value"));

		$webElement = $this->_driver->findElement(By::id("iframe2"));
		$webElement = $this->_targetLocator->getFrameByWebElement($webElement)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe2");

		$this->assertEquals("test iframe2", $webElement->getAttribute("value"));
	}

	public function testGetWindowShouldGetWindowWebElement()
	{
		$this->_driver->findElement(By::id("btnPopUp1"))->click();

		$webElement = $this->_targetLocator->getWindow("popup1")->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window");

		$this->assertEquals("test window", $webElement->getAttribute("value"));
	}
	
	public function testGetWindowShouldGetWindowWebElementGetBackToParentWindow()
	{
		$window1Handle = $this->_driver->getCurrentWindowHandle();

		$this->_driver->findElement(By::id("btnPopUp1"))->click();
		$this->_targetLocator->getWindow($window1Handle);
		$this->_driver->findElement(By::id("btnPopUp2"))->click();

		$webElement = $this->_targetLocator->getWindow("popup1")->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window 1");
		
		$this->assertEquals("test window 1", $webElement->getAttribute("value"));

		$webElement = $this->_targetLocator->getWindow("popup2")->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window 2");
		
		$this->assertEquals("test window 2", $webElement->getAttribute("value"));

		$webElement = $this->_targetLocator->getWindow($window1Handle)->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window default");

		$this->assertEquals("test window default", $webElement->getAttribute("value"));

		$this->_targetLocator->getWindow("popup1")->closeCurrentWindow();
		$this->_targetLocator->getWindow("popup2")->closeCurrentWindow();
		$this->_targetLocator->getWindow($window1Handle)->closeCurrentWindow();
	}

	public function testGetActiveElementShouldGetActiveElement()
	{
		$this->_driver->findElement(By::id("txt1"))->sendKeys("test");
		
		$webElement = $this->_targetLocator->getActiveElement();

		$this->assertEquals("test", $webElement->getAttribute("value"));
	}

	public function testGetAlertShouldGetAlertInstance()
	{
		$this->_driver->findElement(By::id("btnAlert"))->click();

		$alert = $this->_targetLocator->getAlert();
		
		$this->assertTrue($alert instanceof Alert);
	}

	public function testGetAlertShouldGetAlertText()
	{
		$this->_driver->findElement(By::id("btnAlert"))->click();

		$alertText = $this->_targetLocator->getAlert()->getText();

		$this->assertEquals("Here is the alert", $alertText);
	}

	public function testGetAlertShouldDismissAlert()
	{
		$this->_driver->findElement(By::id("btnConfirm"))->click();

		$this->_targetLocator->getAlert()->dismiss();

		$alertText = $this->_targetLocator->getAlert()->getText();

		$this->assertEquals("false", $alertText);
	}

	public function testGetAlertShouldAcceptAlert()
	{
		$this->_driver->findElement(By::id("btnConfirm"))->click();

		$this->_targetLocator->getAlert()->accept();

		$alertText = $this->_targetLocator->getAlert()->getText();

		$this->assertEquals("true", $alertText);
	}

	public function testGetAlertShouldSendKeysToAlert()
	{
		$this->_driver->findElement(By::id("btnPrompt"))->click();

		$alert = $this->_targetLocator->getAlert();
		$alert->sendKeys("alert text");
		$alert->accept();

		$alertText = $this->_targetLocator->getAlert()->getText();

		$this->assertEquals("alert text", $alertText);
	}

    public function testNewTabShouldGetNewWindow()
    {
        $oldHandle1 = $this->_driver->getCurrentWindowHandle();
        $numHandles = count($this->_driver->getCurrentWindowHandles());
        $oldHandle2 = $this->_targetLocator->newTab($this->_url);
        $this->assertEquals($oldHandle1, $oldHandle2);
        $newHandle = $this->_driver->getCurrentWindowHandle();
        $this->assertNotEquals($oldHandle1, $newHandle);
        $this->assertEquals($numHandles + 1, count($this->_driver->getCurrentWindowHandles()));
    }

	//TODO TEST WITH INVALID URL, INVALID PORT INVALID BROWSERNAME
}