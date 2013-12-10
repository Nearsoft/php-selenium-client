<?php

require_once __DIR__ . '/AbstractTest.php';

use Nearsoft\SeleniumClient\WebDriverWait;
use Nearsoft\SeleniumClient\By;

class WebDriverWaitTest extends AbstractTest
{
	public function testUntilShouldWait()
	{
		$this->_driver->findElement(By::id("btnAppendDiv"))->click();
		$wait = new WebDriverWait(8);		
		$label = $wait->until($this->_driver,"findElement",array(By::id("dDiv1-0"),true));
		$this->assertEquals("Some content",$label->getText());
	}
	
	public function testUntilShouldWaitShouldThrowException()
	{
		
		$this->setExpectedException('Nearsoft\SeleniumClient\Exceptions\WebDriverWaitTimeout');
		$this->_driver->findElement(By::id("btnAppendDiv"))->click();
		$wait = new WebDriverWait(3);	
		$label = $wait->until($this->_driver,"findElement",array(By::id("dDiv1-0"),true));	
	}
}