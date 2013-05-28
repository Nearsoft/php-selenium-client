<?php

use SeleniumClient\WebDriver;
use SeleniumClient\WebDriverWait;
use SeleniumClient\WebElement;
use SeleniumClient\By;
use SeleniumClient\WebDriverWaitTimeoutException;

class WebDriverWaitTest extends PHPUnit_Framework_TestCase 
{
	private $_driver = null;
	private $_url = TEST_URL;
	
	public function setUp()
	{
		$this->_driver = new WebDriver();
		$this->_driver->get($this->_url);
	}
	
	public function tearDown()
	{
		if($this->_driver != null)
			$this->_driver->quit();
	}
	
	public function testUntilShouldWait()
	{
		$this->_driver->findElement(By::id("btnAppendDiv"))->click();		
		$wait = new WebDriverWait(8);		
		$label = $wait->until($this->_driver,"findElement",array(By::id("dDiv1-0"),true));		
		$this->assertEquals("Some content",$label->getText());
	}
	
	public function testUntilShouldWaitShouldThrowException()
	{
		
		$this->setExpectedException('SeleniumClient\WebDriverWaitTimeoutException');		
		$this->_driver->findElement(By::id("btnAppendDiv"))->click();	
		$wait = new WebDriverWait(3);	
		$label = $wait->until($this->_driver,"findElement",array(By::id("dDiv1-0"),true));
	
	}
	

}