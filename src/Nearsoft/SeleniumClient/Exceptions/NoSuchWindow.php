<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class NoSuchWindow extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " A request to switch to a different window could not be satisfied because the window could not be found. "  . $message);
	}
}