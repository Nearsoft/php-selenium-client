<?php

require_once __DIR__ . '/AbstractTest.php';

use Nearsoft\SeleniumClient\SelectElement;
use Nearsoft\SeleniumClient\By;

class SelectTest extends AbstractTest
{
	public function testSelectByValueShouldSelect()
	{
		$select = new SelectElement($this->_driver->findElement(By::id("sel1")));
		
		$select->selectByValue("2");

		$this->assertTrue($select->getElement()->findElement(By::xPath("option[@value = 2]"))->isSelected());

		$select = new SelectElement($this->_driver->findElement(By::id("sel2")));

		$select->selectByValue("onions");

		$this->assertTrue($select->getElement()->findElement(By::xPath("option[@value = 'onions']"))->isSelected());
	}
	
	public function testSelectByPartialTextShouldSelect()
	{
		$select = new SelectElement($this->_driver->findElement(By::id("sel1")));
	
		$select->selectByPartialText("Red");
	
		$this->assertTrue($select->getElement()->findElement(By::xPath("option[@value = 2]"))->isSelected());
	
		$select = new SelectElement($this->_driver->findElement(By::id("sel2")));
	
		$select->selectByPartialText("peppers");
	
		$this->assertTrue($select->getElement()->findElement(By::xPath("option[@value = 'greenpeppers']"))->isSelected());
	}
}
	

