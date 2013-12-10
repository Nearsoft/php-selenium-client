<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class InvalidCookieDomain extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " An illegal attempt was made to set a cookie under a different domain than the current page. "  . $message);
	}
}