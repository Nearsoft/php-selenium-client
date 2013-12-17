<?php

require_once __DIR__ . '/../bootstrap.php';

use Nearsoft\SeleniumClient\By;
use Nearsoft\SeleniumClient\WebDriver;
use Nearsoft\SeleniumClient\WebDriverWait;
use Nearsoft\SeleniumClient\DesiredCapabilities;
use Nearsoft\SeleniumClient\CapabilityType;
use Nearsoft\SeleniumClient\BrowserType;
use Nearsoft\SeleniumClient\PlatformType;

class Demo2Test extends PHPUnit_Framework_TestCase
{
    /** @var WebDriver */
	private $_driver = null;

    /** @var string */
	private $_testUrl = null;
	
	public function setUp()
	{
		$this->_testUrl = "http://nearsoft-php-seleniumclient.herokuapp.com/sandbox/";
		
		$desiredCapabilities = new DesiredCapabilities();
		$desiredCapabilities->setCapability(CapabilityType::BROWSER_NAME, BrowserType::FIREFOX);
		$desiredCapabilities->setCapability(CapabilityType::VERSION, "24.0");
		$desiredCapabilities->setCapability(CapabilityType::PLATFORM, PlatformType::WINDOWS);		
		
		$this->_driver = new WebDriver($desiredCapabilities);
		//note that the actual capabilities supported may be different to the desired capabilities specified
	}
	
	public function tearDown()
	{
		if($this->_driver != null) { $this->_driver->quit(); }
	}
	
	public function testDemo2()
	{
		//click button/get alert text
		$this->_driver->get($this->_testUrl);
		$this->_driver->findElement(By::id("btnAlert"))->click();
		$alert = $this->_driver->switchTo()->alert();
		$this->assertEquals("Here is the alert", $alert->getText());
		$alert->accept();
		
		//get main window handle
		$mainWindowHandle = $this->_driver->getWindowHandle();
		
		//open popup window / handle its elements
		$this->_driver->findElement(By::id("btnPopUp1"))->click();
		
		$this->_driver->switchTo()->window("popup1");
		
		$webElement = $this->_driver->waitForElementUntilIsPresent(By::id("txt1"));
		
		$webElement->sendKeys("test window");
		
		$this->assertEquals("test window", $webElement->getAttribute("value"));
		
		$this->_driver->close();
		
		$this->_driver->switchTo()->window($mainWindowHandle);
		
		//get iframe / handle its elements
		$this->_driver->switchTo()->frame("iframe1");
		
		$webElement = $this->_driver->waitForElementUntilIsPresent(By::id("txt1"));
		$webElement->sendKeys("test iframe");
		
		$this->assertEquals("test iframe", $webElement->getAttribute("value"));
	
		$this->_driver->switchTo()->window($mainWindowHandle);
		
		//wait for element to be present
		$this->_driver->findElement(By::id("btnAppendDiv"))->click();
		$wait = new WebDriverWait(8);
		$label = $wait->until($this->_driver,"findElement",array(By::id("dDiv1-0"),true));
		$this->assertEquals("Some content",$label->getText());
		
		sleep(5);
	}
}