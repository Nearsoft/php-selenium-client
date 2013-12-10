<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class IMEEngineActivationFailed extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " An IME engine could not be started. "  . $message);
	}
}