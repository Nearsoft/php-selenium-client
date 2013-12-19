<?php

require_once __DIR__ . '/../test/bootstrap.php';

use Nearsoft\SeleniumClient\By;
use Nearsoft\SeleniumClient\SelectElement;
use Nearsoft\SeleniumClient\WebDriver;
use Nearsoft\SeleniumClient\DesiredCapabilities;

class Demo1Test extends PHPUnit_Framework_TestCase
{
	/** @var WebDriver */
    private $_driver = null;

    /** @var string */
	private $_testUrl = null;
	
	public function setUp()
	{
		$this->_testUrl = "http://nearsoft-php-seleniumclient.herokuapp.com/sandbox/";

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
		sleep(4);
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

		//navigation
		$this->_driver->get('http://www.nearsoft.com');

		$this->_driver->navigate()->refresh();

		$this->_driver->navigate()->back();

		$this->_driver->navigate()->forward();
		
		sleep(5);
	}
}