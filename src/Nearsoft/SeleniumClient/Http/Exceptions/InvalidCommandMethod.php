<?php
namespace Nearsoft\SeleniumClient\Http\Exceptions;

class InvalidCommandMethod extends \Exception {
	public function __construct($httpResponseCode, $url) {
		parent::__construct ( "HTTP response code {$httpResponseCode}. Invalid command method. Url {$url}. If a request path maps to a valid resource, but that resource does not respond to the request method, the server should respond with a 405 Method Not Allowed." );
	}
}
