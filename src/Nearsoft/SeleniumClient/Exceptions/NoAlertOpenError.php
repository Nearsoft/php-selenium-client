<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class NoAlertOpenError extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " An attempt was made to operate on a modal dialog when one was not open. "  . $message);
	}
}