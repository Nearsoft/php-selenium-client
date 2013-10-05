<?php

require_once __DIR__ . '/AbstractTest.php';

use SeleniumClient\Alert;
use SeleniumClient\By;

class AlertTest extends AbstractTest
{
    /** @var Alert */
	private $_alert = null;

	public function setUp()
	{
		parent::setUp();
		$this->_alert = new Alert($this->_driver);
	}

	/*
	 * TODO:
	 * consider no selenium server running
	 * fail case for non existing url
	 * test find element by every location method xpath id css	 *
	 */
	
	public function testGetAlertShouldGetAlertText()
	{
		$this->_driver->findElement(By::id("btnAlert"))->click();

		$alertText = $this->_alert->getText();

		$this->assertEquals("Here is the alert", $alertText);
	}
	
	public function testGetAlertShouldDismissAlert()
	{
		$this->_driver->findElement(By::id("btnConfirm"))->click();

		$this->_alert->dismiss();

		$alertText = $this->_alert->getText();

		$this->assertEquals("false", $alertText);
	}

	public function testGetAlertShouldAcceptAlert()
	{
		$this->_driver->findElement(By::id("btnConfirm"))->click();

		$this->_alert->accept();

		$alertText = $this->_alert->getText();

		$this->assertEquals("true", $alertText);
	}

	public function testGetAlertShouldSendKeysToAlert()
	{
		$this->_driver->findElement(By::id("btnPrompt"))->click();

		$this->_alert->sendKeys("alert text");
		$this->_alert->accept();

		$alertText = $this->_alert->getText();

		$this->assertEquals("alert text", $alertText);
	}
	//TODO TEST WITH INVALID URL, INVALID PORT INVALID BROWSERNAME
}