<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class InvalidSelector extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " Argument was an invalid selector (e.g. XPath/CSS). "  . $message);
	}
}