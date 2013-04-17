<?php

class SeleniumClientAutoLoader {
	
	public function __construct()
	{
		spl_autoload_register(array($this, 'seleniumClientLoader'));
	}

	private function seleniumClientLoader($className)
	{
		include "../../" . str_replace("\\", "/", $className) . '.php';
	}

}

$autoloader = new SeleniumClientAutoLoader();