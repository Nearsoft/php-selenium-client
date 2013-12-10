<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class ElementNotVisible extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " An element command could not be completed because the element is not visible on the page. "  . $message);
	}
}