<?php

require_once __DIR__ . '/AbstractTest.php';

use SeleniumClient\Alert;
use SeleniumClient\By;
use SeleniumClient\TargetLocator;
use SeleniumClient\WebElement;


class WebDriverTest extends AbstractTest
{	
	public function testAcceptAlertShouldGetText()
	{
		$this->_driver->findElement(By::id("btnConfirm"))->click();
		$this->_driver->acceptAlert();
		$this->assertEquals("TRUE", strtoupper($this->_driver->getAlertText()));
	}

	public function testDismissAlertShouldGetText()
	{
		$this->_driver->findElement(By::id("btnConfirm"))->click();
		$this->_driver->dismissAlert();
		$this->assertEquals("FALSE", strtoupper($this->_driver->getAlertText()));
	}
	
	public function testDismissAlertShouldMakeAlertBeClosed()
	{
		$this->_driver->findElement(By::id("btnAlert"))->click();
		$this->_driver->dismissAlert();
		$this->setExpectedException('SeleniumClient\Http\SeleniumNoAlertOpenErrorException');	
		$this->_driver->getAlertText();
	}
	
	public function testSetAlertValueShouldGetText()
	{
		$this->_driver->findElement(By::id("btnPrompt"))->click();
		$this->_driver->setAlertValue("Some value sent");
		$this->_driver->acceptAlert();
		$this->assertEquals("Some value sent", $this->_driver->getAlertText());
	}
	
	public function testGetAlertTextShouldGetText()
	{
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
		$webElement = $this->_driver->switchTo()->getDefaultFrame()->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}
	
	public function testSwitchToFrameByIndexShouldGetFrameWebElement()
	{
		$webElement = $this->_driver->switchTo()->getFrameByIndex(0)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}

	public function testSwitchToFrameByIndexShouldGetFrameWebElementGetBackToParentWindow()
	{
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
		$webElement = $this->_driver->switchTo()->getFrameByName("iframe1")->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}
	
	public function testSwitchToFrameByNameShouldGetFrameWebElementGetBackToParentWindow()
	{
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
		$webElement = $this->_driver->findElement(By::id("iframe1"));
		$webElement = $this->_driver->switchTo()->getFrameByWebElement($webElement)->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}

	public function testSwitchToFrameByWebElementShouldGetFrameWebElementGetBackToParentWindow()
	{
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
		$this->_driver->findElement(By::id("btnPopUp1"))->click();

		$webElement = $this->_driver->switchTo()->getWindow("popup1")->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window");

		$this->assertEquals("test window", $webElement->getAttribute("value"));
	}
	
	public function testSwitchToGetWindowShouldGetWindowWebElementGetBackToParentWindow()
	{
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
		$this->_driver->findElement(By::id("txt1"))->sendKeys("test");
		
		$webElement = $this->_driver->switchTo()->getActiveElement();

		$this->assertEquals("test", $webElement->getAttribute("value"));
	}

	public function testSwitchToGetAlertShouldGetAlertInstance()
	{
		$this->_driver->findElement(By::id("btnAlert"))->click();

		$alert = $this->_driver->switchTo()->getAlert();
		
		$this->assertTrue($alert instanceof Alert);
	}

	public function testSwitchToGetAlertShouldGetAlertText()
	{
		$this->_driver->findElement(By::id("btnAlert"))->click();

		$alertText = $this->_driver->switchTo()->getAlert()->getText();

		$this->assertEquals("Here is the alert", $alertText);
	}

	public function testSwitchToGetAlertShouldDismissAlert()
	{
		$this->_driver->findElement(By::id("btnConfirm"))->click();

		$this->_driver->switchTo()->getAlert()->dismiss();

		$alertText = $this->_driver->switchTo()->getAlert()->getText();

		$this->assertEquals("false", $alertText);
	}

	public function testSwitchToGetAlertShouldAcceptAlert()
	{
		$this->_driver->findElement(By::id("btnConfirm"))->click();

		$this->_driver->switchTo()->getAlert()->accept();

		$alertText = $this->_driver->switchTo()->getAlert()->getText();

		$this->assertEquals("true", $alertText);
	}

	public function testSwitchToGetAlertShouldSendKeysToAlert()
	{
		$this->_driver->findElement(By::id("btnPrompt"))->click();

		$alert = $this->_driver->switchTo()->getAlert();
		$alert->sendKeys("alert text");
		$alert->accept();

		$alertText = $this->_driver->switchTo()->getAlert()->getText();

		$this->assertEquals("alert text", $alertText);
	}

	public function testSetCookie()
	{
		$this->_driver->setCookie("test", "1");
		$cookies = $this->_driver->getCurrentCookies();
		$this->assertEquals('test',$cookies[0]['name']);
	}

	public function testGetCurrentCookies()
	{
		$this->_driver->setCookie("test", "1");
		$this->_driver->setCookie("test2", "2");

		$this->assertEquals(2, count($this->_driver->getCurrentCookies()));
	}

	public function testClearCookieShouldClear()
	{
        $url = parse_url( $this->_url );
        $host = strpos( $url['host'], '.' ) !== false ? $url['host'] : null;

		$this->_driver->setCookie("test", "1");
		$this->_driver->setCookie("test2","2", "/");
		$this->_driver->setCookie("test3", "3", "/", $host, false, 0);

		$this->assertEquals(3, count($this->_driver->getCurrentCookies()));
		$this->_driver->clearCookie("test2");
		$this->assertEquals(2, count($this->_driver->getCurrentCookies()));
		$this->_driver->clearCurrentCookies();
	}
	
	public function testClearCurrentCookiesShouldClear()
	{
        $url = parse_url( $this->_url );
        $host = strpos( $url['host'], '.' ) !== false ? $url['host'] : null;

		$this->_driver->setCookie("test", "1");
		$this->_driver->setCookie("test2", "2", "/");
		$this->_driver->setCookie("test3", "3", "/", $host, false, 0);
		
		$this->assertEquals(3, count($this->_driver->getCurrentCookies()));
		$this->_driver->clearCurrentCookies();
		$this->assertEquals(0, count($this->_driver->getCurrentCookies()));
		$this->_driver->clearCurrentCookies();
	}
	
	public function testSetGetCookiesShouldSetGet()
	{
        $url = parse_url( $this->_url );
        $host = strpos( $url['host'], '.' ) !== false ? $url['host'] : null;

		$this->_driver->setCookie("test", "1");
		$this->_driver->setCookie("test2", "2", "/");
		$this->_driver->setCookie("test3", "3", "/", $host, false, 0);
		
		$this->assertTrue(is_array($this->_driver->getCurrentCookies()));
		$this->assertEquals(3, count($this->_driver->getCurrentCookies()));
		$this->_driver->clearCurrentCookies();
	}

	public function testScreenshotsShouldCreateFile()
	{
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
		$window1Handle = $this->_driver->getCurrentWindowHandle();
		
		$position = $this->_driver->getWindowPosition($window1Handle);
		
		$this->assertTrue(is_numeric($position["x"]));
		$this->assertTrue(is_numeric($position["y"]));
	}
	
	public function testGetCurrentWindowPositionShouldGetArray()
	{
		$position = $this->_driver->getCurrentWindowPosition();
		
		$this->assertTrue(is_numeric($position["x"]));
		$this->assertTrue(is_numeric($position["y"]));
	}
	
	public function testSetWindowPositionShouldGetArray()
	{
		$window1Handle = $this->_driver->getCurrentWindowHandle();

		$this->_driver->setWindowSize($window1Handle, 200,200);
		
		$this->_driver->setWindowPosition($window1Handle, 100, 50);
		
		$position = $this->_driver->getWindowPosition($window1Handle);

		$this->assertEquals(100, $position["x"]);
		$this->assertEquals(50, $position["y"]);
	}
	
	public function testSetCurrentWindowPositionShouldGetArray()
	{
		$this->_driver->setCurrentWindowSize(200,200);
		
		$this->_driver->setCurrentWindowPosition(50, 60);
		
		$position = $this->_driver->getCurrentWindowPosition();
		
		$this->assertEquals(50, $position["x"]);
		$this->assertEquals(60, $position["y"]);
	}
	
	public function testGetWindowSizeShouldGetArray()
	{
		$window1Handle = $this->_driver->getCurrentWindowHandle();
		$dimensions = $this->_driver->getWindowSize($window1Handle);

		$this->assertTrue(is_numeric($dimensions["width"]));
		$this->assertTrue(is_numeric($dimensions["height"]));
	}
	
	public function testGetCurrentWindowSizeShouldGetArray()
	{
		$dimensions = $this->_driver->getCurrentWindowSize();
		
		$this->assertTrue(is_numeric($dimensions["width"]));
		$this->assertTrue(is_numeric($dimensions["height"]));
	}

	public function testSetWindowSizeShouldGetArray()
	{
		$window1Handle = $this->_driver->getCurrentWindowHandle();

		$this->_driver->setWindowSize($window1Handle, 432, 520);
		$dimensions = $this->_driver->getWindowSize($window1Handle);
		
		$this->assertEquals(432, $dimensions["width"]);
		$this->assertEquals(520, $dimensions["height"]);
	}
	
	public function testSetCurrentWindowSizeShouldGetArray()
	{
		$this->_driver->setCurrentWindowSize(432, 520);
		$dimensions = $this->_driver->getCurrentWindowSize();
		
		$this->assertEquals(432, $dimensions["width"]);
		$this->assertEquals(520, $dimensions["height"]);
	}
	
	public function testGetWindowShouldNavigateAcrossWindows()
	{
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
		$this->assertTrue(is_string($this->_driver->getCurrentWindowHandle()));
	}
	
	public function testGetCurrentWindowHandlesSholdGet3Handles()
	{
		$this->_driver->findElement(By::id("btnPopUp1"))->click();
		$this->_driver->findElement(By::id("btnPopUp2"))->click();

		$this->assertEquals(3, count($this->_driver->getCurrentWindowHandles()));
	}

    public function testMaximizeCurrentWindowShouldMaximizeCurrentWindow()
    {
		$dimensionsBefore = $this->_driver->getCurrentWindowSize();
		$this->_driver->maximizeCurrentWindow();
		$dimensionsAfter = $this->_driver->getCurrentWindowSize();
		$this->assertTrue($dimensionsAfter['height'] > $dimensionsBefore['height']);
		$this->assertTrue($dimensionsAfter['width'] > $dimensionsBefore['width']);
    }

    public function testMaximizeWindowShouldMaximizeWindow()
    {
    	$windowHandle = $this->_driver->getCurrentWindowHandle();
    	$dimensionsBefore = $this->_driver->getWindowSize($windowHandle);
		$this->_driver->maximizeWindow($windowHandle);
		$dimensionsAfter = $this->_driver->getWindowSize($windowHandle);
		$this->assertTrue($dimensionsAfter['height'] > $dimensionsBefore['height']);
		$this->assertTrue($dimensionsAfter['width'] > $dimensionsBefore['width']);
	}

	public function testCloseCurrentWindowShouldClose()
	{
		$this->_driver->findElement(By::id("btnPopUp1"))->click();
		$this->_driver->switchTo()->getWindow("popup1");
		$this->_driver->closeCurrentWindow();
		$this->setExpectedException('SeleniumClient\Http\SeleniumNoSuchWindowException');	
		$this->_driver->getCurrentPageUrl();		
	}

	public function testGetWindowShouldAccessContent()
	{
		$this->_driver->findElement(By::id("btnPopUp1"))->click();
		$this->_driver->switchTo()->getWindow("popup1");
		
		$webElement = $this->_driver->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test window");
		
		$this->assertEquals("test window", $webElement->getAttribute("value"));
	}
	
	public function testGetFrameShouldAccessContent()
	{
		$this->_driver->getFrame("iframe1");
		
		$webElement = $this->_driver->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");

		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	}
	
	public function testGetFrameShouldAccessContentGetBackParentWindow()
	{
		$window1Handle = $this->_driver->getCurrentWindowHandle();
		$this->_driver->getFrame("iframe1");

		$webElement = $this->_driver->findElement(By::id("txt1"));
		$webElement->sendKeys("test iframe");
		
		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
		$this->_driver->switchTo()->getWindow($window1Handle);
		
		$webElementParentWindow = $this->_driver->findElement(By::id("txt1"));
		$webElementParentWindow->sendKeys("test parent window 1");

		$this->assertEquals("test parent window 1", $webElementParentWindow->getAttribute("value"));
	}

    public function testGetCurrentSessionsShouldGetArray()
    {
    	$sessions = $this->_driver->getCurrentSessions();
        $this->assertTrue(is_array($sessions));
        $this->assertTrue(count($sessions) > 0);
    }

	public function testExecuteScriptShouldSetInputText()
	{
		$this->_driver->executeScript("document.getElementById('txt2').value='TEST!';");
		$webElement = $this->_driver->findElement(By::id("txt2"));
		
		$this->assertEquals("TEST!", $webElement->getAttribute("value"));
	}
	
	public function testExecuteScriptShouldGetPageTitle()
	{
		$this->assertEquals("Nearsoft SeleniumClient SandBox", $this->_driver->executeScript("return document.title"));
	}
	
	public function testExecuteScriptShouldSetInputTextUsingArguments()
	{
		$this->_driver->executeScript("document.getElementById(arguments[0]).value=arguments[1];", array("txt1", "TEST2!"));

		$webElement = $this->_driver->findElement(By::id("txt1"));
		$this->assertEquals("TEST2!", $webElement->getAttribute("value"));
	}
	
	public function testExecuteAsyncScriptShouldShowAlertKeepDriverInstance()
	{
		//https://code.google.com/p/selenium/wiki/JsonWireProtocol#POST_/session/:sessionId/execute_async
		//There is an implicit last argument being sent which MUST be invoked as callback by the end of the function
		$this->_driver->executeAsyncScript("console.log(arguments);document.getElementById('txt1').value='finished';arguments[arguments.length - 1]();", array('foo','var'));
		$this->assertEquals('finished',$this->_driver->findElement(By::id("txt1"))->getValue());   	
	}

	public function testSetAsyncScriptTimeout()
	{
		$this->_driver->setAsyncScriptTimeout(1);
		$this->setExpectedException('SeleniumClient\Http\SeleniumScriptTimeoutException');	
		$this->_driver->executeAsyncScript("setTimeout('arguments[0]()',5000);");
	}
	
	public function testGetCapabilitiesShouldGetInfo() {
        $this->assertTrue(is_array($this->_driver->getCapabilities()));
    }

	public function testStartSessionShouldHaveSessionId()
	{
		$this->assertNotEquals(null, $this->_driver->getSessionId());
        // SessionID appears to be a 36 character GUID
		$this->assertGreaterThan(0, strlen($this->_driver->getSessionId()));
	}
	
	public function testGetShouldNavigateToUrl()
	{
		$url = $this->_url."/formReceptor.php";
		$this->_driver->get($url);
		$this->assertEquals($url, $this->_driver->getCurrentPageUrl());
	}

	public function testGetCurrentPageUrl()
	{
		$this->assertEquals($this->_url, $this->_driver->getCurrentPageUrl());
	}
	
	public function testSetImplicitWait()
	{
		$this->_driver->findElement(By::id("btnAppendDiv"))->click();
		$this->_driver->setImplicitWait(5000);
		$webElement = $this->_driver->findElement(By::id("dDiv1-0")); // This takes 5 seconds to be present
		$this->assertInstanceOf('SeleniumClient\WebElement', $webElement);
	}
	
	public function testStatus()
	{ 
		$status = $this->_driver->status();
		$expectedKeys = array('status','sessionId','value','state','class','hCode');
		$this->assertTrue(array_intersect(array_keys($status),$expectedKeys) === $expectedKeys);
	}
	
	public function testRefresh() 
	{ 
		$webElement = $this->_driver->findElement(By::id("txt1"));
		$webElement->sendKeys("9999");
		$this->_driver->refresh();
		$webElement = $this->_driver->findElement(By::id("txt1"));
		$this->assertEquals("", $webElement->getAttribute("value"));
	}
	
	public function testPageSource()
	{
		$this->assertTrue((bool)strpos($this->_driver->pageSource(), '<legend>Form elements</legend>'));		
	}
	
	public function testTitle()
	{
		$this->assertTrue(is_string($this->_driver->title()));
		$this->assertTrue(count($this->_driver->title())>0);
	}
	
	public function testFindElement()
	{
		$webElement = $this->_driver->findElement(By::id("txt1"));	
		$this->assertInstanceOf('SeleniumClient\WebElement',$webElement);		

		$this->setExpectedException('SeleniumClient\Http\SeleniumNoSuchElementException');	
		$webElement = $this->_driver->findElement(By::id("NOTEXISTING"));	
	}

	public function testFindElementInElement()
	{
		$parentElement = $this->_driver->findElement(By::id("sel1"));
		$childElement = $this->_driver->findElement(By::xPath(".//option[@value = '3']"), false, $parentElement->getElementId());	
		$this->assertInstanceOf('SeleniumClient\WebElement',$childElement);	
	}

    public function testFindElementByJsSelector()
    {
        $input = $this->_driver->findElement(By::jsSelector('input','document.querySelectorAll'));
        $this->assertInstanceOf('SeleniumClient\WebElement',$input);	
        $this->setExpectedException('SeleniumClient\Http\SeleniumInvalidSelectorException');
        $this->_driver->findElement(By::jsSelector('input'));
    }
	
	public function testFindElements()
	{
		$webElements = $this->_driver->findElements(By::tagName("input"));
		
		foreach($webElements as $webElement) { $this->assertTrue($webElement instanceof  WebElement); }
		
		$this->assertTrue(is_array($webElements));
		$this->assertTrue(count($webElements)>0);
	}

	public function testFindElementsInElement()
	{
		$parentElement = $this->_driver->findElement(By::id("sel1"));
		$childElements = $this->_driver->findElements(By::xPath(".//option"), false, $parentElement->getElementId());	
		$this->assertInternalType('array',$childElements);	
		$this->assertInstanceOf('SeleniumClient\WebElement',$childElements[1]);	
	}

    public function testFindElementsByJsSelector()
    {
        $inputs = $this->_driver->findElements(By::jsSelector('input','document.querySelectorAll'));
        $self = $this;
        array_walk($inputs, function($input) use ($self) {
            $self->assertTrue($input instanceof WebElement);
        });
        $this->assertGreaterThan(0, count($inputs));
        $this->setExpectedException('SeleniumClient\Http\SeleniumInvalidSelectorException');
        $this->_driver->findElements(By::jsSelector('input'));
    }

	public function testBack()
	{
		$expectedTitle = $this->_driver->title();
		
		$this->_driver->get("http://nearsoft.com");
		
		$this->_driver->back();
		
		$this->assertEquals($expectedTitle, $this->_driver->title());
	}
	
	public function testForward()
	{
		$this->_driver->get($this->_url."/formReceptor.php");
		
		$expectedTitle = $this->_driver->title();
		
		$this->_driver->back();
		
		$this->_driver->forward();
		
		$this->assertEquals($expectedTitle, $this->_driver->title());
	}
	
	public function testWaitForElementUntilIsPresent()
	{
		$this->_driver->findElement(By::id("btnAppendDiv"))->click();
		
		$this->_driver->waitForElementUntilIsPresent(By::id("dDiv1-0"),10);
		
		$this->assertEquals("Some content", $this->_driver->findElement(By::id("dDiv1-0"))->getText());
	}
	
	public function testWaitForElementUntilIsNotPresent()
	{
		$webElement = $this->_driver->findElement(By::id("btnHideThis"));
		
		$webElement->click();
		
		$this->_driver->waitForElementUntilIsNotPresent(By::id("btnHideThis"),10);
		
		$this->assertFalse($webElement->isDisplayed());		
	}

	public function testSetPageLoadTimeout()
	{
		$this->_driver->setPageLoadTimeout(1);
		$this->setExpectedException('SeleniumClient\Http\SeleniumScriptTimeoutException');
		$this->_driver->get($this->_url."/formReceptor.php");
	}

	public function testQuit()
	{	
		$anotherDriver = new SeleniumClient\WebDriver();
		$sessionId = $anotherDriver->getSessionId();	

		$containsSession = function($var) use ($sessionId)
		{
			return ($var['id'] == $sessionId);
		};

		$anotherDriver->quit();
		$sessions = $this->_driver->getCurrentSessions();		
		$matchedSessions = array_filter($sessions,$containsSession);
		$this->assertEquals(0, count($matchedSessions));		
	}
}