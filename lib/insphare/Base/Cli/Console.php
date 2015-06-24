<?php
namespace Insphare\Base\Cli;

class Console {

	/**
	 * Windows mode
	 */
	const ENVIRONMENT_WINDOWS = 1;

	/**
	 * Unix mode
	 */
	const ENVIRONMENT_UNIX = 2;

	/**
	 * @var bool
	 */
	private $synchronous = true;

	/**
	 * @var bool
	 */
	private $preventOutput = false;
	/**
	 * @var array
	 */
	private $commands = array();

	/**
	 * @param bool $preventOutput
	 */
	public function __construct($preventOutput = false) {
		$this->preventOutput = $preventOutput;
	}

	/**
	 * @param bool $executeBackground
	 * @return $this
	 */
	public function setSynchronous($executeBackground) {
		$this->synchronous = (bool)$executeBackground;

		return $this;
	}

	/**
	 * @return bool
	 */
	private function getSynchronous() {
		return (bool)$this->synchronous;
	}

	/**
	 * @return int
	 */
	private function getEnvironment() {
		$uName = strtolower(php_uname());
		if (substr($uName, 0, 7) == "windows") {
			return self::ENVIRONMENT_WINDOWS;
		}

		return self::ENVIRONMENT_UNIX;
	}

	/**
	 * @param $command
	 * @param bool $escape
	 * @return $this
	 */
	public function addCommand($command, $escape = true) {
		if (true === $escape) {
			$command = escapeshellcmd($command);
		}

		$this->commands[] = $command;
		return $this;
	}

	/**
	 * @return array
	 */
	private function getCommands() {
		return $this->commands;
	}

	/**
	 * @internal param $command
	 * @return int|string
	 */
	public function execute() {
		$output = '';
		foreach ($this->getCommands() as $command) {
			$this->output('Command: ' . ($command));
			$this->output('Async: ' . (int)!$this->getSynchronous());

			if (true === $this->getSynchronous()) {
				$output .= $this->executeSynchronous($command);
			}
			else {
				$output .= $this->executeAsynchronous($command);
			}
		}

		return $output;
	}

	/**
	 * @param $message
	 */
	private function output($message) {
		if (false === $this->preventOutput && PHP_SAPI === 'cli') {
			echo '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
		}
	}

	/**
	 * @param $string
	 * @return string
	 */
	public function escapeArguments($string) {
		return escapeshellarg($string);
	}

	/**
	 * @param $command
	 * @return int|string
	 */
	private function executeSynchronous($command) {
		if ($this->getEnvironment() === self::ENVIRONMENT_WINDOWS) {
			return pclose(popen("start " . $command, "r"));
		}
		else {
			return shell_exec($command);
		}
	}

	/**
	 * @param $command
	 * @return int|string
	 */
	private function executeAsynchronous($command) {
		if ($this->getEnvironment() === self::ENVIRONMENT_WINDOWS) {
			return pclose(popen("start /B " . $command, "r"));
		}
		else {
			return shell_exec($command . " > /dev/null &");
		}
	}
}
