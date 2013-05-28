<?php

require_once 'AutoLoader.php';

use SeleniumClient\By;
use SeleniumClient\SelectElement;
use SeleniumClient\WebDriver;
use SeleniumClient\DesiredCapabilities;

class AlertTest extends PHPUnit_Framework_TestCase
{
	private $_driver = null;
	private $_testUrl = null;
	
	public function setUp()
	{
		$this->_testUrl = "http://nearsoft-php-seleniumclient.herokuapp.com/SeleniumClientTest/SandBox/";
		
		$desiredCapabilities = new DesiredCapabilities("firefox");
		
		$this->_driver = new WebDriver($desiredCapabilities);
	}
	
	public function tearDown()
	{
		if($this->_driver != null) { $this->_driver->quit(); }
	}
	
	public function testDemo1()
	{
		//get url
		$this->_driver->get($this->_testUrl);
		
		//access text input
		$webElement = $this->_driver->findElement(By::id("txt1"));		
		$webElement->clear();		
		$webElement->sendKeys("Text sent 1");		
		
		$this->assertEquals("Text sent 1", $webElement->getAttribute("value"));
		
		$webElement = $this->_driver->findElement(By::id("txt2"));
		$webElement->clear();
		$webElement->sendKeys("Text sent 2");		
		
		$this->assertEquals("Text sent 2", $webElement->getAttribute("value"));
		
		//access listbox
		$selectElement = new SelectElement($this->_driver->findElement(By::id("sel1")));	
		$selectElement->selectByValue("4");
		
		$this->assertTrue($selectElement->getElement()->findElement(By::xPath(".//option[@value = 4]"))->isSelected());
				
		//access checkbox
		$webElement = $this->_driver->findElement(By::cssSelector("html body table tbody tr td fieldset form p input#chk3"));
		$webElement->click();		
		$this->assertTrue($webElement->isSelected());
		
		//access radio
		$webElement = $this->_driver->findElement(By::id("rd3"));
		$webElement->click();
		
		$this->assertTrue($webElement->isSelected());
		
		//access button
		$webElement = $this->_driver->findElement(By::id("btnSubmit"));
		$webElement->click();
		
		//access h2
		$webElement = $this->_driver->findElement(By::cssSelector("html body h2#h2FormReceptor"));
		$this->assertEquals("Form receptor", $webElement->getText());
		
		sleep(20);
	}
}