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

	public function testSwitchToGetAlertShouldGetAlertInstance()
	{
		$this->_driver->findElement(By::id("btnAlert"))->click();

		$alert = $this->_driver->switchTo()->alert();
		
		$this->assertTrue($alert instanceof Alert);
	}

	public function testSwitchToGetAlertShouldGetAlertText()
	{
		$this->_driver->findElement(By::id("btnAlert"))->click();

		$alertText = $this->_driver->switchTo()->alert()->getText();

		$this->assertEquals("Here is the alert", $alertText);
	}

	public function testSwitchToGetAlertShouldDismissAlert()
	{
		$this->_driver->findElement(By::id("btnConfirm"))->click();

		$this->_driver->switchTo()->alert()->dismiss();

		$alertText = $this->_driver->switchTo()->alert()->getText();

		$this->assertEquals("false", $alertText);
	}

	public function testSwitchToGetAlertShouldAcceptAlert()
	{
		$this->_driver->findElement(By::id("btnConfirm"))->click();

		$this->_driver->switchTo()->alert()->accept();

		$alertText = $this->_driver->switchTo()->alert()->getText();

		$this->assertEquals("true", $alertText);
	}

	public function testSwitchToGetAlertShouldSendKeysToAlert()
	{
		$this->_driver->findElement(By::id("btnPrompt"))->click();

		$alert = $this->_driver->switchTo()->alert();
		$alert->sendKeys("alert text");
		$alert->accept();

		$alertText = $this->_driver->switchTo()->alert()->getText();

		$this->assertEquals("alert text", $alertText);
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
	
	public function testGetWindowHandlesholdGetHandle()
	{
		$this->assertTrue(is_string($this->_driver->getWindowHandle()));
	}
	
	public function testGetWindowHandlesSholdGet3Handles()
	{
		$this->_driver->findElement(By::id("btnPopUp1"))->click();
		$this->_driver->findElement(By::id("btnPopUp2"))->click();

		$this->assertEquals(3, count($this->_driver->getWindowHandles()));
	}	

    public function testGetCurrentSessionsShouldGetArray()
    {
    	$sessions = $this->_driver->getCurrentSessions();
        $this->assertTrue(is_array($sessions));
        $this->assertTrue(count($sessions) > 0);
    }

    public function testSwitchShouldGetTargetLocatorInstance()
	{	
		$this->assertInstanceOf('SeleniumClient\TargetLocator', $this->_driver->switchTo());		
	}

	 public function testManageShouldGetOptionsInstance()
	{	
		$this->assertInstanceOf('SeleniumClient\Options', $this->_driver->manage());		
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
		$this->assertEquals($url, $this->_driver->getCurrentUrl());
	}

	public function testGetCurrentUrl()
	{
		$this->assertEquals($this->_url, $this->_driver->getCurrentUrl());
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
		$this->assertEquals(asort(array_keys($status)),asort($expectedKeys));
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