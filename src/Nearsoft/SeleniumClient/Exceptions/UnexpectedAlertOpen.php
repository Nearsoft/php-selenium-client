<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class UnexpectedAlertOpen extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " A modal dialog was open, blocking this operation. "  . $message);
	}
}