<?php

use SeleniumClient\Alert;
use SeleniumClient\By;
use SeleniumClient\Cookie;
use SeleniumClient\DesiredCapabilities;
use SeleniumClient\TargetLocator;
use SeleniumClient\WebDriver;
use SeleniumClient\WebDriverWait;
use SeleniumClient\WebElement;


class WebDriverTest extends PHPUnit_Framework_TestCase
{
	private $_driver = null;
	private $_url = TEST_URL;
	
	public function setUp() { $this->_driver = new WebDriver(); }
	
	public function tearDown()
	{
		if($this->_driver != null) { $this->_driver->quit(); }
	}
	
	/*
	 * TODO:
	 * consider no selenium server running
	 * fail case for non existing url
	 * test find element by every location method xpath id css	 *
	 */
	
	public function testAcceptAlertShouldGetText()
	{
		$this->_driver->get($this->_url);
		$this->_driver->findElement(By::id("btnConfirm"))->click();
		$this->_driver->acceptAlert();
		$this->assertEquals("TRUE", strtoupper($this->_driver->getAlertText()));
	}
	
	public function testDismissAlertShouldGetText()
	{
		$this->_driver->get($this->_url);
		$this->_driver->findElement(By::id("btnConfirm"))->click();
		$this->_driver->dismissAlert();
		$this->assertEquals("FALSE", strtoupper($this->_driver->getAlertText()));
	}
	
	public function testSetPromptTextShouldGetText()
	{
		$this->_driver->get($this->_url);
		$this->_driver->findElement(By::id("btnPrompt"))->click();
		$this->_driver->setAlertValue("Some value sent");
		$this->_driver->acceptAlert();
		$this->assertEquals("Some value sent", $this->_driver->getAlertText());
	}
	
	public function testGetAlertTextShouldGetText()
	{
		$this->_driver->get($this->_url);
		$this->_driver->findElement(By::id("btnAlert"))->click();
		$this->assertEquals("Here is the alert", $this->_driver->getAlertText());
	}
	
	public function testSwitchToShouldGetTargetLocatorInstance()
	{
		$result = $this->_driver->switchTo();

		$this->assertTrue($result instanceof TargetLocator);
	}

	public function testSwitchToDefaultFrameShouldGetFrameWebElement()
	{
		$this->_driver->get($this->_url);

		$webElement = $this->_driver->switchTo()->getDefaultFrame()->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}
	
	public function testSwitchToFrameByIndexShouldGetFrameWebElement()
	{
		$this->_driver->get($this->_url);

		$webElement = $this->_driver->switchTo()->getFrameByIndex(0)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}

	public function testSwitchToFrameByIndexShouldGetFrameWebElementGetBackToParentWindow()
	{
		$this->_driver->get($this->_url);
		
		$webElement = $this->_driver->switchTo()->getFrameByIndex(0)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));

		$webElement = $this->_driver->switchTo()->getDefaultFrame()->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe default");

		$this->assertEquals("test iframe default", $webElement->getAttribute("value"));

		$webElement = $this->_driver->switchTo()->getFrameByIndex(1)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe1");
		
		$this->assertEquals("test iframe1", $webElement->getAttribute("value"));
	}

	public function testSwitchToFrameByNameShouldGetFrameWebElement()
	{
		$this->_driver->get($this->_url);

		$webElement = $this->_driver->switchTo()->getFrameByName("iframe1")->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}
	
	public function testSwitchToFrameByNameShouldGetFrameWebElementGetBackToParentWindow()
	{
		$this->_driver->get($this->_url);
		
		$webElement = $this->_driver->switchTo()->getFrameByName("iframe1")->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));

		$webElement = $this->_driver->switchTo()->getDefaultFrame()->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe default");

		$this->assertEquals("test iframe default", $webElement->getAttribute("value"));

		$webElement = $this->_driver->switchTo()->getFrameByName("iframe2")->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe2");

		$this->assertEquals("test iframe2", $webElement->getAttribute("value"));
	}
	
	public function testSwitchToFrameByWebElementShouldGetFrameWebElement()
	{
		$this->_driver->get($this->_url);

		$webElement = $this->_driver->findElement(By::id("iframe1"));
		$webElement = $this->_driver->switchTo()->getFrameByWebElement($webElement)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}

	public function testSwitchToFrameByWebElementShouldGetFrameWebElementGetBackToParentWindow()
	{
		$this->_driver->get($this->_url);

		$webElement = $this->_driver->findElement(By::id("iframe1"));
		$webElement = $this->_driver->switchTo()->getFrameByWebElement($webElement)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));

		$webElement = $this->_driver->switchTo()->getDefaultFrame()->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe default");

		$this->assertEquals("test iframe default", $webElement->getAttribute("value"));

		$webElement = $this->_driver->findElement(By::id("iframe2"));
		$webElement = $this->_driver->switchTo()->getFrameByWebElement($webElement)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe2");

		$this->assertEquals("test iframe2", $webElement->getAttribute("value"));
	}

	public function testSwitchToGetWindowShouldGetWindowWebElement()
	{
		$this->_driver->get($this->_url);

		$this->_driver->findElement(By::id("btnPopUp1"))->click();

		$webElement = $this->_driver->switchTo()->getWindow("popup1")->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window");

		$this->assertEquals("test window", $webElement->getAttribute("value"));
	}
	
	public function testSwitchToGetWindowShouldGetWindowWebElementGetBackToParentWindow()
	{
		$this->_driver->get($this->_url);

		$window1Handle = $this->_driver->getCurrentWindowHandle();

		$this->_driver->findElement(By::id("btnPopUp1"))->click();
		$this->_driver->switchTo()->getWindow($window1Handle);
		$this->_driver->findElement(By::id("btnPopUp2"))->click();

		$webElement = $this->_driver->switchTo()->getWindow("popup1")->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window 1");
		
		$this->assertEquals("test window 1", $webElement->getAttribute("value"));

		$webElement = $this->_driver->switchTo()->getWindow("popup2")->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window 2");
		
		$this->assertEquals("test window 2", $webElement->getAttribute("value"));

		$webElement = $this->_driver->switchTo()->getWindow($window1Handle)->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window default");

		$this->assertEquals("test window default", $webElement->getAttribute("value"));

		$this->_driver->switchTo()->getWindow("popup1")->closeCurrentWindow();
		$this->_driver->switchTo()->getWindow("popup2")->closeCurrentWindow();
		$this->_driver->switchTo()->getWindow($window1Handle)->closeCurrentWindow();
	}

	public function testSwitchToGetActiveElementShouldGetActiveElement()
	{
		$this->_driver->get($this->_url);
		
		$this->_driver->findElement(By::id("txt1"))->sendKeys("test");
		
		$webElement = $this->_driver->switchTo()->getActiveElement();

		$this->assertEquals("test", $webElement->getAttribute("value"));
	}

	public function testSwitchToGetAlertShouldGetAlertInstance()
	{
		$this->_driver->get($this->_url);
		
		$this->_driver->findElement(By::id("btnAlert"))->click();

		$alert = $this->_driver->switchTo()->getAlert();
		
		$this->assertTrue($alert instanceof Alert);
	}

	public function testSwitchToGetAlertShouldGetAlertText()
	{
		$this->_driver->get($this->_url);
		
		$this->_driver->findElement(By::id("btnAlert"))->click();

		$alertText = $this->_driver->switchTo()->getAlert()->getText();

		$this->assertEquals("Here is the alert", $alertText);
	}

	public function testSwitchToGetAlertShouldDismissAlert()
	{
		$this->_driver->get($this->_url);
		
		$this->_driver->findElement(By::id("btnConfirm"))->click();

		$this->_driver->switchTo()->getAlert()->dismiss();

		$alertText = $this->_driver->switchTo()->getAlert()->getText();

		$this->assertEquals("false", $alertText);
	}

	public function testSwitchToGetAlertShouldAcceptAlert()
	{
		$this->_driver->get($this->_url);
		
		$this->_driver->findElement(By::id("btnConfirm"))->click();

		$this->_driver->switchTo()->getAlert()->accept();

		$alertText = $this->_driver->switchTo()->getAlert()->getText();

		$this->assertEquals("true", $alertText);
	}

	public function testSwitchToGetAlertShouldSendKeysToAlert()
	{
		$this->_driver->get($this->_url);
		
		$this->_driver->findElement(By::id("btnPrompt"))->click();

		$alert = $this->_driver->switchTo()->getAlert();
		$alert->sendKeys("alert text");
		$alert->accept();

		$alertText = $this->_driver->switchTo()->getAlert()->getText();

		$this->assertEquals("alert text", $alertText);
	}

	public function testClearCookieShouldClear()
	{
		$this->_driver->get($this->_url);
		$this->_driver->setCookie("test", "1");
		$this->_driver->setCookie("test2","2", "/");
		$this->_driver->setCookie("test3", "3", "/", TEST_DOMAIN, true, 0);

		$this->assertEquals(3, count($this->_driver->getCurrentCookies()));
		$this->_driver->clearCookie("test2");
		$this->assertEquals(2, count($this->_driver->getCurrentCookies()));
		$this->_driver->clearCurrentCookies();
	}
	
	public function testClearCurrentCookiesShouldClear()
	{
		$this->_driver->get($this->_url);
		$this->_driver->setCookie("test", "1");
		$this->_driver->setCookie("test2", "2", "/");
		$this->_driver->setCookie("test3", "3", "/", TEST_DOMAIN, true, 0);
		
		$this->assertEquals(3, count($this->_driver->getCurrentCookies()));
		$this->_driver->clearCurrentCookies();
		$this->assertEquals(0, count($this->_driver->getCurrentCookies()));
		$this->_driver->clearCurrentCookies();
	}
	
	public function testSetGetCookiesShouldSetGet()
	{
		$this->_driver->get($this->_url);
		$this->_driver->setCookie("test", "1");
		$this->_driver->setCookie("test2", "2", "/");
		$this->_driver->setCookie("test3", "3", "/", TEST_DOMAIN, true, 0);
		
		$this->assertTrue(is_array($this->_driver->getCurrentCookies()));
		$this->assertEquals(3, count($this->_driver->getCurrentCookies()));
		$this->_driver->clearCurrentCookies();
	}

	public function testScreenshotsShouldCreateFile()
	{
		$this->_driver->get($this->_url);

		$screenShotsDirectory = "/tmp/selenium screenshots";
		
		if (!file_exists($screenShotsDirectory)) { mkdir($screenShotsDirectory, 0755, true); }
		$this->_driver->setScreenShotsDirectory($screenShotsDirectory);
		
		$generatedFileNames[] = $this->_driver->screenshot();

		$webElementOnPopUp1 = $this->_driver->findElement(By::id("txt1"));
		$webElementOnPopUp1->sendKeys("test for screenshot");
		
		$generatedFileNames[] = $this->_driver->screenshot();
		
		$webElementOnPopUp1 = $this->_driver->findElement(By::id("txt2"));
		$webElementOnPopUp1->sendKeys("test for screenshot 2");
		
		$generatedFileNames[] = $this->_driver->screenshot();
		
		$this->assertEquals(3, count($generatedFileNames));
		
		foreach($generatedFileNames as $generatedFileName)
		{
			$this->assertTrue(file_exists($this->_driver->getScreenShotsDirectory() . "/". $this->_driver->getSessionId() . "/".  $generatedFileName));
		}
	}
	
	public function testScreenshotsShouldCreateFile2()
	{
		$this->_driver->get($this->_url);
	
		$screenShotsDirectory = "/tmp/selenium screenshots";
		
		if (!file_exists($screenShotsDirectory)) {
			mkdir($screenShotsDirectory, 0755, true);
		}
		$this->_driver->setScreenShotsDirectory($screenShotsDirectory);
	
		$generatedFileNames[] = $this->_driver->screenshot($screenShotsDirectory);
	
		$webElementOnPopUp1 = $this->_driver->findElement(By::id("txt1"));
		$webElementOnPopUp1->sendKeys("test for screenshot");
	
		$generatedFileNames[] = $this->_driver->screenshot($screenShotsDirectory);
	
		$webElementOnPopUp1 = $this->_driver->findElement(By::id("txt2"));
		$webElementOnPopUp1->sendKeys("test for screenshot 2");
	
		$generatedFileNames[] = $this->_driver->screenshot($screenShotsDirectory);
	
		$this->assertEquals(3, count($generatedFileNames));
	
		foreach($generatedFileNames as $generatedFileName)
		{
			$this->assertTrue(file_exists($this->_driver->getScreenShotsDirectory() . "/". $this->_driver->getSessionId() . "/".  $generatedFileName));
		}
	}
	
	public function testGetWindowPositionShouldGetArray()
	{
		$this->_driver->get($this->_url);
		
		$window1Handle = $this->_driver->getCurrentWindowHandle();
		
		$position = $this->_driver->getWindowPosition($window1Handle);
		
		$this->assertTrue(is_numeric($position["x"]));
		$this->assertTrue(is_numeric($position["y"]));
	}
	
	public function testGetCurrentWindowPositionShouldGetArray()
	{
		$this->_driver->get($this->_url);
		$position = $this->_driver->getCurrentWindowPosition();
		
		$this->assertTrue(is_numeric($position["x"]));
		$this->assertTrue(is_numeric($position["y"]));
	}
	
	public function testSetWindowPositionShouldGetArray()
	{
		$this->_driver->get($this->_url);
		$window1Handle = $this->_driver->getCurrentWindowHandle();

		$this->_driver->setWindowSize($window1Handle, 200,200);
		
		$this->_driver->setWindowPosition($window1Handle, 100, 50);
		
		$position = $this->_driver->getWindowPosition($window1Handle);

		$this->assertEquals(100, $position["x"]);
		$this->assertEquals(50, $position["y"]);
	}
	
	public function testSetCurrentWindowPositionShouldGetArray()
	{
		$this->_driver->get($this->_url);
		
		$this->_driver->setCurrentWindowSize(200,200);
		
		$this->_driver->setCurrentWindowPosition(50, 60);
		
		$position = $this->_driver->getCurrentWindowPosition();
		
		$this->assertEquals(50, $position["x"]);
		$this->assertEquals(60, $position["y"]);
	}
	
	public function testGetWindowSizeShouldGetArray()
	{
		$this->_driver->get($this->_url);		
		$window1Handle = $this->_driver->getCurrentWindowHandle();
		$dimensions = $this->_driver->getWindowSize($window1Handle);

		$this->assertTrue(is_numeric($dimensions["width"]));
		$this->assertTrue(is_numeric($dimensions["height"]));
	}
	
	public function testGetCurrentWindowSizeShouldGetArray()
	{
		$this->_driver->get($this->_url);
		$dimensions = $this->_driver->getCurrentWindowSize();
		
		$this->assertTrue(is_numeric($dimensions["width"]));
		$this->assertTrue(is_numeric($dimensions["height"]));
	}

	public function testSetWindowSizeShouldGetArray()
	{
		$this->_driver->get($this->_url);
		$window1Handle = $this->_driver->getCurrentWindowHandle();

		$this->_driver->setWindowSize($window1Handle, 432, 520);
		$dimensions = $this->_driver->getWindowSize($window1Handle);
		
		$this->assertEquals(432, $dimensions["width"]);
		$this->assertEquals(520, $dimensions["height"]);
	}
	
	public function testSetCurrentWindowSizeShouldGetArray()
	{
		$this->_driver->get($this->_url);
		$this->_driver->setCurrentWindowSize(432, 520);
		$dimensions = $this->_driver->getCurrentWindowSize();
		
		$this->assertEquals(432, $dimensions["width"]);
		$this->assertEquals(520, $dimensions["height"]);
	}
	
	public function testGetWindowShouldNavigateAcrossWindows()
	{
		$this->_driver->get($this->_url);
		$this->_driver->setImplicitWait(5000);
		
		$window1Handle = $this->_driver->getCurrentWindowHandle();
		$this->_driver->findElement(By::id("btnPopUp1"))->click();
		$this->_driver->switchTo()->getWindow("popup1");
		
		$webElementOnPopUp1 = $this->_driver->findElement(By::id("txt1"));
		$webElementOnPopUp1->sendKeys("test popup window 1");
		
		$this->assertEquals("test popup window 1", $webElementOnPopUp1->getAttribute("value"));
		
		$this->_driver->closeCurrentWindow();
		$this->_driver->switchTo()->getWindow($window1Handle);
		
		$webElementParentWindow = $this->_driver->findElement(By::id("txt1"));
		$webElementParentWindow->sendKeys("test parent window 1");

		$this->assertEquals("test parent window 1", $webElementParentWindow->getAttribute("value"));
	}
	
	public function testGetCurrentWindowHandleSholdGetHandle()
	{
		$this->_driver->get($this->_url);
		$this->assertTrue(is_string($this->_driver->getCurrentWindowHandle()));
	}
	
	public function testGetCurrentWindowHandlesSholdGet3Handles()
	{
		$this->_driver->get($this->_url);
		$this->_driver->findElement(By::id("btnPopUp1"))->click();
		$this->_driver->findElement(By::id("btnPopUp2"))->click();

		$this->assertEquals(3, count($this->_driver->getCurrentWindowHandles()));
	}
	
	public function testCloseCurrentWindowShouldClose()
	{
		$this->_driver->get($this->_url);
		$this->_driver->findElement(By::id("btnPopUp1"))->click();
		$this->_driver->switchTo()->getWindow("popup1");
		$this->_driver->closeCurrentWindow();
		
		$this->assertNotEquals(null, $this->_driver);
	}

	public function testGetWindowShouldAccessContent()
	{
		$this->_driver->get($this->_url);
		$this->_driver->findElement(By::id("btnPopUp1"))->click();
		$this->_driver->switchTo()->getWindow("popup1");
		
		$webElement = $this->_driver->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window");
		
		$this->assertEquals("test window", $webElement->getAttribute("value"));
	}
	
	public function testGetFrameShouldAccessContent()
	{
		$this->_driver->get($this->_url);
		$this->_driver->getFrame("iframe1");
		
		$webElement = $this->_driver->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}
	
	public function testGetFrameShouldAccessContentGetBackParentWindow()
	{
		$this->_driver->get($this->_url);
		$window1Handle = $this->_driver->getCurrentWindowHandle();
		$this->_driver->getFrame("iframe1");

		$webElement = $this->_driver->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");
		
		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
		sleep(5);
		$this->_driver->switchTo()->getWindow($window1Handle);
		
		$webElementParentWindow = $this->_driver->findElement(By::id("txt1"));
		$webElementParentWindow->sendKeys("test parent window 1");

		$this->assertEquals("test parent window 1", $webElementParentWindow->getAttribute("value"));
		sleep(5);
	}
	
	public function testGetCurrentSessionsShouldGetArray() { $this->assertTrue(is_array($this->_driver->getCurrentSessions())); }

	public function testExecuteScriptShouldSetInputText()
	{
		$this->_driver->get($this->_url);
		$this->_driver->executeScript("document.getElementById('txt2').value='TEST!';");
		$webElement = $this->_driver->findElement(By::id("txt2"));
		
		$this->assertEquals("TEST!", $webElement->getAttribute("value"));
	}
	
	public function testExecuteScriptShouldGetPageTitle()
	{
		$this->_driver->get($this->_url);
		$this->assertEquals("Nearsoft SeleniumClient SandBox", $this->_driver->executeScript("return document.title"));
	}
	
	public function testExecuteScriptShouldSetInputTextUsingArguments()
	{
		$this->_driver->get($this->_url);
		$this->_driver->executeScript("document.getElementById(arguments[0]).value=arguments[1];", array("txt1", "TEST2!"));

		$webElement = $this->_driver->findElement(By::id("txt1"));
		$this->assertEquals("TEST2!", $webElement->getAttribute("value"));
	}
	
	public function testSetAsyncScriptTimeout()
	{
		$this->_driver->get($this->_url);
		$this->_driver->setAsyncScriptTimeout(10000);
		$this->assertNotEquals(null, $this->_driver);
	}
	
	public function testExecuteAsyncScriptShouldShowAlertKeepDriverInstance()
	{
		$this->_driver->get($this->_url);
		$this->_driver->executeAsyncScript("arguments[arguments.length - 1](document.body);");
		$this->assertNotEquals(null, $this->_driver);
	}
	
	public function testGetCapabilitiesShouldGetInfo() { $this->assertTrue(is_array($this->_driver->getCapabilities())); }

	public function testStartSessionShouldHaveSessionId()
	{
		$this->assertNotEquals(null, $this->_driver->getSessionId());
		$this->assertTrue(count($this->_driver->getSessionId())>0);
	}
	
	public function testGetShouldNavigateToUrl()
	{
		$this->_driver->get($this->_url);
		$this->assertEquals($this->_url, $this->_driver->getCurrentPageUrl());
	}
	
	public function testSetImplicitWait()
	{
		$this->_driver->setImplicitWait(1000);
		$this->assertNotEquals(null, $this->_driver);
	}
	
	public function testStatus()
	{ 
		$this->assertTrue(is_array($this->_driver->status())); 
	}
	
	public function testRefresh() 
	{ 
		$this->assertNotEquals(null, $this->_driver); 
	}
	
	public function testPageSource()
	{
		$this->_driver->get($this->_url);
		$this->assertTrue(is_string($this->_driver->pageSource()));
		$this->assertTrue(count($this->_driver->pageSource())>0);
	}
	
	public function testTitle()
	{
		$this->_driver->get($this->_url);
		$this->assertTrue(is_string($this->_driver->title()));
		$this->assertTrue(count($this->_driver->title())>0);
	}
	
	public function testFindElement()
	{
		$this->_driver->get($this->_url);
		
		$webElement = $this->_driver->findElement(By::id("txt1"));
		
		$this->assertTrue($webElement instanceof  WebElement);
		
		$webElement->clear();
		
		$webElement->sendKeys("9999");
		
		$this->assertEquals("9999", $webElement->getAttribute("value"));

	}
	
	public function testFindElements()
	{
		$this->_driver->get($this->_url);
		
		$webElements = $this->_driver->findElements(By::tagName("input"));
		
		foreach($webElements as $webElement) { $this->assertTrue($webElement instanceof  WebElement); }
		
		$this->assertTrue(is_array($webElements));
		$this->assertTrue(count($webElements)>0);
	}
	
	public function testBack()
	{
		$this->_driver->get($this->_url);
		
		$expectedTitle = $this->_driver->title();
		
		$this->_driver->get($this->_url."/free-tools/");
		
		$this->_driver->back();
		
		$this->assertEquals($expectedTitle, $this->_driver->title());
	}
	
	public function testForward()
	{
		$this->_driver->get($this->_url);
		
		$this->_driver->get($this->_url."/formReceptor.php");
		
		$expectedTitle = $this->_driver->title();
		
		$this->_driver->back();
		
		$this->_driver->forward();
		
		$this->assertEquals($expectedTitle, $this->_driver->title());
	}
	
	public function testWaitForElementUntilIsPresent()
	{
		$this->_driver->get($this->_url);
		
		$this->_driver->findElement(By::id("btnAppendDiv"))->click();
		
		$this->_driver->waitForElementUntilIsPresent(By::id("dDiv1-0"),10);
		
		$this->assertEquals("Some content", $this->_driver->findElement(By::id("dDiv1-0"))->getText());
	}
	
	public function testWaitForElementUntilIsNotPresent()
	{
		$this->_driver->get($this->_url);	
		
		$webElement = $this->_driver->findElement(By::id("btnHideThis"));
		
		$webElement->click();
		
		$this->_driver->waitForElementUntilIsNotPresent(By::id("btnHideThis"),10);
		
		$this->assertFalse($webElement->isDisplayed());		
	}
	
	//TODO TEST WITH INVALID URL, INVALID PORT INVALID BROWSERNAME
}