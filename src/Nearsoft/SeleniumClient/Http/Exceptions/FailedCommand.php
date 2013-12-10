<?php
namespace Nearsoft\SeleniumClient\Http\Exceptions;

class FailedCommand extends \Exception {
	public function __construct($httpResponseCode, $url) {
		parent::__construct ( "HTTP response code {$httpResponseCode}. Command failed. Url {$url}." );
	}
}