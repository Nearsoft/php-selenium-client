<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class JavaScriptError extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " An error occurred while executing user supplied JavaScript. "  . $message);
	}
}