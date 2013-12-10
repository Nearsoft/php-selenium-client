<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class InvalidElementState extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " An element command could not be completed because the element is in an invalid state (e.g. attempting to click a disabled element). "  . $message);
	}
}