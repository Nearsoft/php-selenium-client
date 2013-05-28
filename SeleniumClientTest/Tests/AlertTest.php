<?php

use SeleniumClient\Alert;
use SeleniumClient\By;
use SeleniumClient\WebDriver;

class AlertTest extends PHPUnit_Framework_TestCase
{
	private $_driver = null;
	private $_alert = null;
	private $_url = TEST_URL;
	
	public function setUp()
	{
		$this->_driver = new WebDriver();
		$this->_alert = new Alert($this->_driver);
	}
	
	public function tearDown()
	{
		if($this->_driver != null) { $this->_driver->quit(); }
	}
	
	/*
	 * TODO:
	 * consider no selenium server running
	 * fail case for non existing url
	 * test find element by every location method xpath id css	 *
	 */
	
	public function testGetAlertShouldGetAlertText()
	{
		$this->_driver->get($this->_url);
		
		$this->_driver->findElement(By::id("btnAlert"))->click();

		$alertText = $this->_alert->getText();

		$this->assertEquals("Here is the alert", $alertText);
	}
	
	public function testGetAlertShouldDismissAlert()
	{
		$this->_driver->get($this->_url);
		
		$this->_driver->findElement(By::id("btnConfirm"))->click();

		$this->_alert->dismiss();

		$alertText = $this->_alert->getText();

		$this->assertEquals("false", $alertText);
	}

	public function testGetAlertShouldAcceptAlert()
	{
		$this->_driver->get($this->_url);
		
		$this->_driver->findElement(By::id("btnConfirm"))->click();

		$this->_alert->accept();

		$alertText = $this->_alert->getText();

		$this->assertEquals("true", $alertText);
	}

	public function testGetAlertShouldSendKeysToAlert()
	{
		$this->_driver->get($this->_url);
		
		$this->_driver->findElement(By::id("btnPrompt"))->click();

		$this->_alert->sendKeys("alert text");
		$this->_alert->accept();

		$alertText = $this->_alert->getText();

		$this->assertEquals("alert text", $alertText);
	}
	//TODO TEST WITH INVALID URL, INVALID PORT INVALID BROWSERNAME
}