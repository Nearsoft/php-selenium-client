<?php

require_once __DIR__ . '/AbstractTest.php';

use Nearsoft\SeleniumClient\By;
use Nearsoft\SeleniumClient\TargetLocator;
use Nearsoft\SeleniumClient\WebElement;


class WebDriverTest extends AbstractTest
{
    private $_navigationMock = null;
    private $_windowMock = null;
    private $_targetLocatorMock = null;
    private $_originalNavigate = null;
    private $_originalWindow = null;
    private $_originalTargetLocator = null;

    public function setUp()
    {
        parent::setUp();
        $this->_navigationMock = $this->getMock('Navigation', array('refresh','to','back','forward'));
        $this->_windowMock = $this->getMock('Window', array('maximize','close' ,'setSize', 'getSize', 'setPosition', 'getPosition'));
        $this->_targetLocatorMock = $this->getMock('TargetLocator', array('window','frame'));
        $this->_originalNavigate = $this->_driver->navigate();
        $this->_originalWindow = $this->_driver->manage()->window();
        $this->_originalTargetLocator = $this->_driver->switchTo();
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

    public function testNavigateShouldGetNavigationInstance()
	{	
		$this->assertInstanceOf('Nearsoft\SeleniumClient\Navigation', $this->_driver->navigate());
	}

    public function testSwitchToShouldGetTargetLocatorInstance()
	{	
		$this->assertInstanceOf('Nearsoft\SeleniumClient\TargetLocator', $this->_driver->switchTo());
	}

	 public function testManageShouldGetOptionsInstance()
	{	
		$this->assertInstanceOf('Nearsoft\SeleniumClient\Options', $this->_driver->manage());
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
	
	public function testExecuteAsyncScriptShouldManipulateDom()
	{
		//https://code.google.com/p/selenium/wiki/JsonWireProtocol#POST_/session/:sessionId/execute_async
		//There is an implicit last argument being sent which MUST be invoked as callback by the end of the function
		$this->_driver->executeAsyncScript("console.log(arguments);document.getElementById('txt1').value='finished';arguments[arguments.length - 1]();", array('foo','var'));
		$this->assertEquals('finished',$this->_driver->findElement(By::id("txt1"))->getValue());   	
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

	public function testStatus()
	{ 
		$status = $this->_driver->status();
		$expectedKeys = array('status','sessionId','value','state','class','hCode');
		$this->assertEquals(asort(array_keys($status)),asort($expectedKeys));
	}

	public function testGetPageSource()
	{
		$this->assertTrue((bool)strpos($this->_driver->getPageSource(), '<legend>Form elements</legend>'));		
	}
	
	public function testGetTitle()
	{
		$this->assertTrue(is_string($this->_driver->getTitle()));
		$this->assertTrue(count($this->_driver->getTitle())>0);
	}
	
	public function testFindElement()
	{
		$webElement = $this->_driver->findElement(By::id("txt1"));	
		$this->assertInstanceOf('Nearsoft\SeleniumClient\WebElement',$webElement);

		$this->setExpectedException('Nearsoft\SeleniumClient\Exceptions\NoSuchElement');
		$webElement = $this->_driver->findElement(By::id("NOTEXISTING"));	
	}

	public function testFindElementInElement()
	{
		$parentElement = $this->_driver->findElement(By::id("sel1"));
		$childElement = $this->_driver->findElement(By::xPath(".//option[@value = '3']"), false, $parentElement->getElementId());	
		$this->assertInstanceOf('Nearsoft\SeleniumClient\WebElement',$childElement);
	}

    public function testFindElementByJsSelector()
    {
        $input = $this->_driver->findElement(By::jsSelector('input','document.querySelectorAll'));
        $this->assertInstanceOf('Nearsoft\SeleniumClient\WebElement',$input);
        $this->setExpectedException('Nearsoft\SeleniumClient\Exceptions\InvalidSelector');
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
		$this->assertInstanceOf('Nearsoft\SeleniumClient\WebElement',$childElements[1]);
	}

    public function testFindElementsByJsSelector()
    {
        $inputs = $this->_driver->findElements(By::jsSelector('input','document.querySelectorAll'));
        $self = $this;
        array_walk($inputs, function($input) use ($self) {
            $self->assertTrue($input instanceof WebElement);
        });
        $this->assertGreaterThan(0, count($inputs));
        $this->setExpectedException('Nearsoft\SeleniumClient\Exceptions\InvalidSelector');
        $this->_driver->findElements(By::jsSelector('input'));
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

	public function testQuit()
	{	
		$anotherDriver = new Nearsoft\SeleniumClient\WebDriver();
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

	public function testCloseWindowShouldClose()
	{
		$this->_driver->findElement(By::id("btnPopUp1"))->click();
		$this->_driver->switchTo()->window("popup1");
		$this->_driver->close();
		$this->setExpectedException('Nearsoft\SeleniumClient\Exceptions\NoSuchWindow');
		$this->_driver->getCurrentUrl();		
	}

    public function testMagicNavigationBackShouldCallMethodBack()
    {
        $this->_navigationMock->expects($this->exactly(1))
            ->method('back');

        $this->_driver->setNavigate($this->_navigationMock);

        $this->_driver->navigationBack();

        $this->_driver->setNavigate($this->_originalNavigate);
    }

    public function testMagicNavigationForwardShouldCallMethodForward()
    {
        $this->_navigationMock->expects($this->exactly(1))
            ->method('forward');

        $this->_driver->setNavigate($this->_navigationMock);

        $this->_driver->navigationForward();

        $this->_driver->setNavigate($this->_originalNavigate);
    }

    public function testMagicNavigationRefreshShouldCallMethodRefresh()
    {
        $this->_navigationMock->expects($this->exactly(1))
            ->method('refresh');

        $this->_driver->setNavigate($this->_navigationMock);

        $this->_driver->navigationRefresh();

        $this->_driver->setNavigate($this->_originalNavigate);
    }

    public function testMagicNavigationToShouldCallMethodTo()
    {
        $this->_navigationMock->expects($this->exactly(1))
            ->method('to')
            ->with($this->equalTo('google.com'));

        $this->_driver->setNavigate($this->_navigationMock);

        $this->_driver->navigationTo('google.com');

        $this->_driver->setNavigate($this->_originalNavigate);
    }

    public function testMagicWindowMaximizeShouldCallMethodMaximize()
    {
        $this->_windowMock->expects($this->exactly(1))
             ->method('maximize');

        $this->_driver->manage()->setWindow($this->_windowMock);

        $this->_driver->windowMaximize();

        $this->_driver->manage()->setWindow($this->_originalWindow);
    }

    public function testMagicWindowGetPositionShouldCallMethodGetPosition()
    {
        $this->_windowMock->expects($this->exactly(1))
            ->method('getPosition');

        $this->_driver->manage()->setWindow($this->_windowMock);

        $this->_driver->windowGetPosition();

        $this->_driver->manage()->setWindow($this->_originalWindow);
    }

    public function testMagicWindowGetSizeShouldCallMethodGetSize()
    {
        $this->_windowMock->expects($this->exactly(1))
            ->method('getSize');

        $this->_driver->manage()->setWindow($this->_windowMock);

        $this->_driver->windowGetSize();

        $this->_driver->manage()->setWindow($this->_originalWindow);

    }

    public function testMagicWindowSetSizeShouldCallMethodSetSize()
    {
        $this->_windowMock->expects($this->exactly(1))
            ->method('setSize')
            ->with($this->equalTo(100), $this->equalTo(100));

        $this->_driver->manage()->setWindow($this->_windowMock);

        $this->_driver->windowSetSize(100,100);

        $this->_driver->manage()->setWindow($this->_originalWindow);

    }

    public function testMagicWindowSetPositionShouldCallMethodSetPosition()
    {
        $this->_windowMock->expects($this->exactly(1))
            ->method('setPosition')
            ->with($this->equalTo(200), $this->equalTo(300));

        $this->_driver->manage()->setWindow($this->_windowMock);

        $this->_driver->windowSetPosition(200,300);

        $this->_driver->manage()->setWindow($this->_originalWindow);

    }

    public function testMagicSwitchToWindowShouldCallMethodWindow()
    {
        $this->_targetLocatorMock->expects($this->exactly(1))
            ->method('window');

        $this->_driver->setSwitchTo($this->_targetLocatorMock);

        $this->_driver->switchToWindow('popup1');

        $this->_driver->setSwitchTo($this->_originalTargetLocator);

    }

    public function testMagicSwitchToFrameShouldCallMethodFrame()
    {
        $this->_targetLocatorMock->expects($this->exactly(1))
            ->method('frame');

        $this->_driver->setSwitchTo($this->_targetLocatorMock);

        $this->_driver->switchToFrame(null);

        $this->_driver->setSwitchTo($this->_originalTargetLocator);
    }

}