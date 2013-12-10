<?php
namespace Nearsoft\SeleniumClient\Http\Exceptions;

class InvalidRequest extends \Exception {
	public function __construct($httpResponseCode, $url) {
		parent::__construct ( "HTTP response code {$httpResponseCode}.Invalid request. Url {$url}. Could be unknown command or variable resource not found" );
	}
}
