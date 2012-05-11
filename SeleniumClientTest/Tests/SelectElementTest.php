<?php

use SeleniumClient\SelectElement;
use SeleniumClient\By;
use SeleniumClient\WebDriver;

class SelectTest extends PHPUnit_Framework_TestCase
{
	private $_driver = null;
	private $_alert = null;
	private $_url = "http://nsoft.hostei.com/";
	
	public function setUp()
	{
		$this->_driver = new WebDriver();
	}
	
	public function tearDown()
	{
		if($this->_driver != null) { $this->_driver->quit(); }
	}
	
	public function testSelectByValudShouldSelect()
	{
		$this->_driver->get($this->_url);
		
		$select = new SelectElement($this->_driver->findElement(By::id("sel1")));
		
		$select->selectByValue("2");

		$this->assertTrue($select->getElement()->findElement(By::xPath("option[@value = 2]"))->isSelected());
	}
}
	

