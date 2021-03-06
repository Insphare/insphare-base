<?php
namespace Insphare\Base;

/**
 * Class EnvironmentVars
 * @package Insphare\Base
 */
class EnvironmentVars {

	const USER_ID = 'insphare.env.user_id';

	const BASE_PATH = 'base.path';
	const CONFIG_PATH = 'config.path';
	const CACHE_PATH = 'cache.path';
	const CRON_PATH = 'cron.path';
	const EXTERNAL_PATH = 'external.path';
	const CONSOLE_PATH = 'console.path';
	const TOOLS_PATH = 'tools.path';
	const IS_DEVELOPMENT_MODE = 'system.environment.is_development';
	const APPLICATION_NAME = 'app.name';
	const ROUTE_DIRECTORY = 'route.directory';
	const REQUEST_URI = 'server.request_uri';
	const CURRENT_ROUTE = 'http.current.route';
	const ENTITY_DIR = 'entity.dir';
	const MVC_CORE_DIRECTORY = 'mvc.core.directory';
	const IS_MOBILE_MODE = 'is.mobile.mode';

	/**
	 * @var array
	 */
	private static $variableStorage = array();

	/**
	 * Never initialize this class.
	 */
	private function __construct() {}

	/**
	 * this class should never be cloned
	 */
	private function __clone() {
	}

	// @codeCoverageIgnoreStop

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
