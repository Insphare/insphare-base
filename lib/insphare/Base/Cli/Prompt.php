<?php
namespace Insphare\Base\Cli;


ignore_user_abort(true);
declare(ticks = 1);

/**
 * Class Prompt
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Prompt {

	/**
	 * @var bool
	 */
	private static $breakSignal = false;

	/**
	 *
	 */
	public function __construct() {
		// Prevent breaking out of the console.
		$this->registerHandler();
		self::setBreakSignal(false);
	}

	/**
	 * @return bool
	 */
	public static function getBreakSignal() {
		return self::$breakSignal;
	}

	/**
	 * @param $state
	 */
	public static function setBreakSignal($state) {
		self::$breakSignal = (bool)$state;
	}

	/**
	 * Prevent breaking out of the console.
	 */
	private function registerHandler() {
		$sigHandler = array(
			$this,
			'signalHandler'
		);
		pcntl_signal(SIGINT, $sigHandler);
		pcntl_signal(SIGTSTP, $sigHandler);
	}

	/**
	 * @param $signal
	 */
	protected function signalHandler($signal) {
		switch ($signal) {
			case SIGINT:
			case SIGTSTP:
				self::$breakSignal = true;
				break;
		}
	}

	/**
	 * @return string
	 */
	public function promptToUserInput() {
		self::$breakSignal = false;
		stream_set_blocking(STDIN, true);
		$handle = fopen('php://stdin', 'r');
		$line = fgets($handle);
		$line = trim($line);
		return $line;
	}
}
