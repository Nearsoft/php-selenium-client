<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class StaleElementReference extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " An element command failed because the referenced element is no longer attached to the DOM. "  . $message);
	}
}