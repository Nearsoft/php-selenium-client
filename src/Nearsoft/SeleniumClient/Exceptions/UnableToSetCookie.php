<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class UnableToSetCookie extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " A request to set a cookie's value could not be satisfied. "  . $message);
	}
}