<?php

use SeleniumClient\CapabilityType;
use SeleniumClient\DesiredCapabilities;

class DesiredCapabilitiesTest extends PHPUnit_Framework_TestCase {	
	
	private $_capability = null;
	
	public function setUp() 
	{		
		
	}
	
	public function tearDown() 
	{
		
	}	
	
	public function testDesiredCapabilityShouldConstruct()
	{
		$this->_capability = new DesiredCapabilities('firefox','3.6','windows');
		
		$this->assertEquals(3, count($this->_capability->getCapabilities()));
		
		//print_r($this->_capability->getCapabilities());
		
		$this->assertEquals("firefox", $this->_capability->getBrowserName());
		$this->assertEquals("firefox", $this->_capability->getCapability("browserName"));
		
		$this->assertEquals("3.6", $this->_capability->getVersion());
		$this->assertEquals("3.6", $this->_capability->getCapability("version"));
		
		$this->assertEquals("windows", $this->_capability->getPlatform());
		$this->assertEquals("windows", $this->_capability->getCapability("platform"));
		
		$this->_capability->setCapability("acceptSslCerts",1);		
		$this->assertEquals(1, $this->_capability->getCapability("acceptSslCerts"));
		
		$this->_capability->setCapability(CapabilityType::javascriptEnabled,1);
		$this->assertEquals(1, $this->_capability->getCapability(CapabilityType::javascriptEnabled));
		
		$this->assertEquals(5, count($this->_capability->getCapabilities()));
		
		//print_r($this->_capability->getCapabilities());
		
	}

}