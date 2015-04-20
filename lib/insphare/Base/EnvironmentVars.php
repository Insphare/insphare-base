<?php
namespace Insphare\Base;

/**
 * Class EnvironmentVars
 * @package Insphare\Base
 */
class EnvironmentVars {

	const USER_ID = 'insphare.env.user_id';

	/**
	 * @var array
	 */
	private static $variableStorage = array();

	/**
	 * Never initialize this class.
	 */
	private function __construct() {}

	/**
	 * @param string $key
	 * @param mixed $mixedValue
	 */
	public static function set($key, $mixedValue) {
		self::$variableStorage[(string)$key] = $mixedValue;
	}

	/**
	 * @param string $key
	 * @return null
	 */
	public static function get($key) {
		return isset(self::$variableStorage[(string)$key]) ? self::$variableStorage[(string)$key] : null;
	}
}
