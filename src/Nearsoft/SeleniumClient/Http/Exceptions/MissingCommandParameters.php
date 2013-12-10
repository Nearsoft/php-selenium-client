<?php
namespace Nearsoft\SeleniumClient\Http\Exceptions;

class MissingCommandParameters extends \Exception {
	public function __construct($httpResponseCode, $url) {
		parent::__construct ( "HTTP response code {$httpResponseCode}. Missing JSON parameters. Url {$url}." );
	}
}