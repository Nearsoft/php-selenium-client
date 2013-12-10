<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class XPathLookupError extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " An error occurred while searching for an element by XPath. "  . $message);
	}
}