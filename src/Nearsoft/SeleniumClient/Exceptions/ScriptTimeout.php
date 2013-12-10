<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class ScriptTimeout extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " A script did not complete before its timeout expired. "  . $message);
	}
}