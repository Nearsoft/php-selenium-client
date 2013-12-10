<?php

require_once __DIR__ . '/AbstractTest.php';

use Nearsoft\SeleniumClient\By;
use Nearsoft\SeleniumClient\TargetLocator;
use Nearsoft\SeleniumClient\WebElement;

class OptionsTest extends AbstractTest
{	
	public function testAddCookie()
	{
		$url = parse_url( $this->_url );
		$host = strpos( $url['host'], '.' ) !== false ? $url['host'] : null;

		$this->_driver->manage()->addCookie("test", "1");

		$expiry = time() + 604800;

		$this->_driver->manage()->addCookie("test2", "2", "/", $host, false, $expiry);

		$cookies = $this->_driver->manage()->getCookies();

		$this->assertEquals('test',$cookies[0]->getName());
		$this->assertEquals('1',$cookies[0]->getValue());

		$this->assertEquals('test2',$cookies[1]->getName());
		$this->assertEquals('2',$cookies[1]->getValue());
		$this->assertEquals('/',$cookies[1]->getPath());
		$this->assertEquals($host,$cookies[1]->getDomain());
		$this->assertEquals(false,$cookies[1]->getSecure());
		$this->assertEquals($expiry,$cookies[1]->getExpiry());
	}

	public function testGetCookies()
	{
		$this->_driver->manage()->addCookie("test", "1");
		$this->_driver->manage()->addCookie("test2", "2");

		$this->assertEquals(2, count($this->_driver->manage()->getCookies()));
	}

	public function testDeleteCookieNamedShouldDelete()
	{
        $url = parse_url( $this->_url );
        $host = strpos( $url['host'], '.' ) !== false ? $url['host'] : null;

		$this->_driver->manage()->addCookie("test", "1");
		$this->_driver->manage()->addCookie("test2","2", "/");
		$this->_driver->manage()->addCookie("test3", "3", "/", $host, false, 0);

		$this->assertEquals(3, count($this->_driver->manage()->getCookies()));
		$this->_driver->manage()->deleteCookieNamed("test2");
		$this->assertEquals(2, count($this->_driver->manage()->getCookies()));

		$cookie3 = $this->_driver->manage()->getCookieNamed("test3");
		$this->assertEquals("test3", $cookie3->getName());
		$this->_driver->manage()->deleteCookie($cookie3);
		$this->assertEquals(1, count($this->_driver->manage()->getCookies()));

		$this->_driver->manage()->deleteAllCookies();
	}
	
	public function testDeleteAllCookiesCookiesShouldClear()
	{
        $url = parse_url( $this->_url );
        $host = strpos( $url['host'], '.' ) !== false ? $url['host'] : null;

		$this->_driver->manage()->addCookie("test", "1");
		$this->_driver->manage()->addCookie("test2", "2", "/");
		$this->_driver->manage()->addCookie("test3", "3", "/", $host, false, 0);
		
		$this->assertEquals(3, count($this->_driver->manage()->getCookies()));
		$this->_driver->manage()->deleteAllCookies();
		$this->assertEquals(0, count($this->_driver->manage()->getCookies()));
		$this->_driver->manage()->deleteAllCookies();
	}
	
	public function testSetGetCookiesShouldSetGet()
	{
        $url = parse_url( $this->_url );
        $host = strpos( $url['host'], '.' ) !== false ? $url['host'] : null;

		$this->_driver->manage()->addCookie("test", "1");
		$this->_driver->manage()->addCookie("test2", "2", "/");
		$this->_driver->manage()->addCookie("test3", "3", "/", $host, false, 0);
		
		$this->assertTrue(is_array($this->_driver->manage()->getCookies()));
		$this->assertEquals(3, count($this->_driver->manage()->getCookies()));
		$this->_driver->manage()->deleteAllCookies();
	}
}