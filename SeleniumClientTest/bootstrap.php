<?php

class SeleniumClientAutoLoader {
	
	public function __construct()
	{
		spl_autoload_register(array($this, 'seleniumClientLoader'));
	}

	private function seleniumClientLoader($className)
	{
		include "../" . str_replace("\\", "/", $className) . '.php';
	}

}

new SeleniumClientAutoLoader();

define("TEST_DOMAIN", "nearsoft-php-seleniumclient.herokuapp.com");
define("TEST_URL", "http://".TEST_DOMAIN."/SeleniumClientTest/SandBox/");