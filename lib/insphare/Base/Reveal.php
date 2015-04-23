<?php
namespace Insphare\Base;

class Reveal {

	private static $callback = array();

	public static function injectCallback(callable $objectClass, $methodName) {
		self::$callback = array($objectClass, $methodName);
	}

	public static function getUserId() {
		if (empty(self::$callback)) {
			throw new Exception('Please register an callback first. This can be done with: Reveal::injectCallback()');
		}

		return call_user_func_array(self::$callback, array());
	}

}