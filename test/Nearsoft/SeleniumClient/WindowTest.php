<?php

require_once __DIR__ . '/AbstractTest.php';

use Nearsoft\SeleniumClient\Alert;
use Nearsoft\SeleniumClient\By;
use Nearsoft\SeleniumClient\TargetLocator;


class WindowTest extends AbstractTest
{
	public function testMaximizeWindowShouldMaximizeWindow()
    {
		$dimensionsBefore = $this->_driver->manage()->window()->getSize();
		$this->_driver->manage()->window()->maximize();
		$dimensionsAfter = $this->_driver->manage()->window()->getSize();
		$this->assertTrue($dimensionsAfter['height'] > $dimensionsBefore['height']);
		$this->assertTrue($dimensionsAfter['width'] > $dimensionsBefore['width']);
    }
    
	public function testSizeShouldSetGet()
	{
		$width = 235; $height = 318;
		$this->_driver->manage()->window()->setSize($width, $height);
		$dimensions = $this->_driver->manage()->window()->getSize();		
		$this->assertEquals($width, $dimensions["width"]);
		$this->assertEquals($height, $dimensions["height"]);
	}

	public function testPositionShouldSetGet()
	{
		$x = 55; $y = 66;
		$this->_driver->manage()->window()->setPosition($x, $y);
		$dimensions = $this->_driver->manage()->window()->getPosition();		
		$this->assertEquals($x, $dimensions["x"]);
		$this->assertEquals($y, $dimensions["y"]);
	}
}