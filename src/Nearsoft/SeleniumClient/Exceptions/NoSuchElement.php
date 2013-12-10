<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class NoSuchElement extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " An element could not be located on the page using the given search parameters. "  . $message);
	}
}