<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class Timeout extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " An operation did not complete before its timeout expired. "  . $message);
	}
}