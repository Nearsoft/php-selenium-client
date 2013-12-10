<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class InvalidElementCoordinates extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " The coordinates provided to an interactions operation are invalid. "  . $message);
	}
}