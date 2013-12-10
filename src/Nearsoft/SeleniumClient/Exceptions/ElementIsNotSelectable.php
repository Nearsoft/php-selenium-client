<?php
namespace Nearsoft\SeleniumClient\Exceptions;

class ElementIsNotSelectable extends \Exception {
	public function __construct($message = "") {
		parent::__construct ( " An attempt was made to select an element that cannot be selected. "  . $message);
	}
}