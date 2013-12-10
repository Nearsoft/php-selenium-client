<?php
namespace Nearsoft\SeleniumClient\Http\Exceptions;

class UnimplementedCommand extends \Exception {
	public function __construct($httpResponseCode, $url) {
		parent::__construct ( "HTTP response code {$httpResponseCode}. Unimplemented Commands. Url {$url}." );
	}
}