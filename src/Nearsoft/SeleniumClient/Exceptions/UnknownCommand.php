<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class UnknownCommand extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " The requested resource could not be found, or a request was received using an HTTP method that is not supported by the mapped resource. "  . $message);
	}
}