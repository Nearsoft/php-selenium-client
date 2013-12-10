<?php
// Copyright 2012-present Nearsoft, Inc

// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at

// http://www.apache.org/licenses/LICENSE-2.0

// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

namespace Nearsoft\SeleniumClient;

use Nearsoft\SeleniumClient\CapabilityType;

class DesiredCapabilities {	
	
	private $_capabilities = null;
	
	/**
	 * Create DesiredCapabilities
	 * @param String $browser
	 * @param String $version
	 * @param String $platform
	 */
	public function __construct($browser = null, $version = null, $platform = null)
	{
		if(isset($browser))
		{
			$this->setCapability(CapabilityType::BROWSER_NAME, $browser);
		}
	
		if(isset($version))
		{
			$this->setCapability(CapabilityType::VERSION, $version);
		}
	
		if(isset($platform))
		{
			$this->setCapability(CapabilityType::PLATFORM, $platform);
		}
	}
	
	/**
	 * Gets current capabilities
	 * @return Array
	 */
	public function getCapabilities()
	{
		return $this->_capabilities;
	}
	
	/**
	 * Gets specified capability
	 * @param String $capabilityType
	 * @throws \Exception
	 * @return String
	 */
	public function getCapability($capabilityType)
	{
		if(!CapabilityType::isValidCapabilityType($capabilityType))
		{
			throw new \Exception("'".$capabilityType ."' is not an valid capability type");
		}
		else if(!isset($this->_capabilities[$capabilityType]))
		{
			return null;
		}
		else
		{
			return $this->_capabilities[$capabilityType];
		}
	}
	
	/**
	 * Gets browser name
	 * @return String
	 */
	public function getBrowserName()
	{
		return $this->getCapability(CapabilityType::BROWSER_NAME);
	}
	
	/**
	 * Gets platform name
	 * @return String
	 */
	public function getPlatform()
	{
		return $this->getCapability(CapabilityType::PLATFORM);
	}
	
	/**
	 * Gets version
	 * @return String
	 */
	public function  getVersion()
	{
		return $this->getCapability(CapabilityType::VERSION);
	}
	
	/**
	 * Gets whether javascript is enabled
	 * @return String
	 */
	public function getIsJavaScriptEnabled()
	{
		return $this->getCapability(CapabilityType::JAVASCRIPT_ENABLED);
	}	
	
	/**
	 * Sets specified capability
	 * @param String $capabilityType
	 * @param String $value
	 * @throws \Exception
	 */
	public function setCapability($capabilityType,$value)
	{	
		if($this->isValidCapabilityAndValue($capabilityType,$value))
		{
			$this->_capabilities[$capabilityType] = $value;
		}
	}

	private function isValidCapabilityAndValue($capabilityType,$value)
	{
		if(CapabilityType::isValidCapabilityType($capabilityType))
		{
			switch($capabilityType)
			{
				case CapabilityType::BROWSER_NAME:
					if(!BrowserType::isValidBrowserType($value)) { throw new Exception("'{$value}' is not a valid browser type"); }
					break;
				case CapabilityType::PLATFORM:
					if(!PlatformType::isValidPlatformType($value)) { throw new Exception("'{$value}' is not a valid platform type"); }
					break;
			}
		}
		else
		{
			throw new Exception("'{$capabilityType}' is not a valid capability type");
		}

		return true;
	}
	
	public function __toString()
	{
		$result = "DesiredCapabilities{BrowserName = " . $this->getBrowserName() ;
		
		if($this->getVersion())
		{
			$result .= " Version = " . $this->getVersion();
		}
		
		if($this->getPlatform())
		{
			$result.= " Platform = " . $this->getPlatform();
		}
	
		$result.= "}";
		
		return $result;
	}
}