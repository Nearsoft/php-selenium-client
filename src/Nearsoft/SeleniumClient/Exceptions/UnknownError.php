<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class UnknownError extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " An unknown server-side error occurred while processing the command. "  . $message);
	}
}