<?php

use Nearsoft\SeleniumClient\CapabilityType;
use Nearsoft\SeleniumClient\DesiredCapabilities;
use Nearsoft\SeleniumClient\BrowserType;
use Nearsoft\SeleniumClient\PlatformType;


class DesiredCapabilitiesTest extends PHPUnit_Framework_TestCase {	
	
	private $_capability = null;
	
	public function testDesiredCapabilityShouldConstruct()
	{
		$this->_capability = new DesiredCapabilities(BrowserType::FIREFOX,'3.6',PlatformType::WINDOWS);
		
		$this->assertEquals(3, count($this->_capability->getCapabilities()));
		
		$this->assertEquals(BrowserType::FIREFOX, $this->_capability->getBrowserName());
		$this->assertEquals(BrowserType::FIREFOX, $this->_capability->getCapability(CapabilityType::BROWSER_NAME));
		
		$this->assertEquals("3.6", $this->_capability->getVersion());
		$this->assertEquals("3.6", $this->_capability->getCapability(CapabilityType::VERSION));
		
		$this->assertEquals(PlatformType::WINDOWS, $this->_capability->getPlatform());
		$this->assertEquals(PlatformType::WINDOWS, $this->_capability->getCapability(CapabilityType::PLATFORM));
		
		$this->_capability->setCapability(CapabilityType::ACCEPT_SSL_CERTS,1);		
		$this->assertEquals(1, $this->_capability->getCapability(CapabilityType::ACCEPT_SSL_CERTS));
		
		$this->_capability->setCapability(CapabilityType::JAVASCRIPT_ENABLED,1);
		$this->assertEquals(1, $this->_capability->getCapability(CapabilityType::JAVASCRIPT_ENABLED));
		
		$this->assertEquals(5, count($this->_capability->getCapabilities()));				
	}

}