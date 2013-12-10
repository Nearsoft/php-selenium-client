<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class NoSuchFrame extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " A request to switch to a frame could not be satisfied because the frame could not be found. "  . $message);
	}
}