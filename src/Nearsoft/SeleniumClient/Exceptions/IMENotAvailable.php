<?php
namespace SeleniumClient\Exceptions;

class IMENotAvailable extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " IME was not available. "  . $message);
	}
}