<?php
namespace Insphare\Base;

class Exception extends \Exception {

	const BAD_REQUEST = 400;
	const UNAUTHORIZED = 401;
	const FORBIDDEN = 403;
	const NOT_FOUND = 404;
	const INTERNAL = 500;
	const NOT_IMPLEMENTED = 501;
	const UNAVAILABLE = 503;
	const UNEXPECTED_EXCEPTION = 5000;

}
