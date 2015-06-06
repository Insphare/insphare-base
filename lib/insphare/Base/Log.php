<?php
namespace Insphare\Base;

class Log {

	private $streamLogFile = 'stream.log';

	/**
	 * @var string
	 */
	private $customLogFile;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @param string $file
	 *
	 * @return Log
	 */
	public static function get($file = 'error.log') {

		$basePath = EnvironmentVars::get(EnvironmentVars::BASE_PATH);
		$logger = new self($basePath . 'logs' . DIRECTORY_SEPARATOR, $file);

		return $logger;
	}

	/**
	 * @param $logFile
	 */
	private function createLogFile($logFile) {

		$logFile = $this->path . $logFile;
		if (!file_exists($this->path)) {
			mkdir($this->path, 0777, true);
		}

		if (!file_exists($logFile)) {
			file_put_contents($logFile, '');
		}

		$errorLogLevelBefore = error_reporting(0);
		chmod($logFile, 0777);
		error_reporting($errorLogLevelBefore);
	}

	/**
	 * @param $path
	 * @param $customLogFile
	 */
	public function __construct($path, $customLogFile) {

		$this->path = $path;
		$this->createLogFile($customLogFile);
		$this->createLogFile($this->streamLogFile);

		$this->customLogFile = $path . $customLogFile;
		$this->streamLogFile = $path . $this->streamLogFile;
	}

	/**
	 * @return bool
	 */
	private function isDevelopmentMode() {

		return EnvironmentVars::get(EnvironmentVars::IS_DEVELOPMENT_MODE);
	}

	/**
	 * @param Exception $e
	 *
	 * @return array
	 */
	public function exception(Exception $e) {

		$message = array();
		$message[] = '';
		$message[] = '---------------------------------------------------------------------';
		$message[] = '[Exception] - ' . date('Y-m-d H:i:s') . ' ';
		$message[] = 'Class: ' . get_class($e);
		$message[] = 'Code: ' . $e->getCode();
		$message[] = 'Message: ' . $e->getMessage();
		$message[] = 'Thrown by file: ' . $e->getFile() . ' (Line: ' . $e->getLine() . ') ';

		$trace = explode(PHP_EOL, $e->getTraceAsString());
		$count = count($trace) - 1;

		if ($count > 0) {
			$message[] = 'Trace:';
		}

		$x = 0;
		for ($i = $count; $i >= 0; $i--) {
			$tr = $trace[$i];
			$tr = preg_replace('~^#(\d+)~', '#' . $x, $tr);
			$message[] = $tr;
			$x++;
		}

		$message[] = '---------------------------------------------------------------------';
		$message[] = '';

		$this->writeArrayToErrorLog($message);

		return $message;
	}

	/**
	 * @param $errorCode
	 * @param $errorMessage
	 * @param $file
	 * @param $line
	 */
	public function error($errorCode, $errorMessage, $file, $line) {

		switch ($errorCode) {
			case E_NOTICE:
			case E_USER_NOTICE:
				$show = 'NOTICE';
				break;

			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$show = 'DEPRECATED';
				break;

			case E_WARNING:
			case E_USER_WARNING:
				$show = 'WARNING';
				break;

			default:
				$show = 'ERROR';
				break;
		}

		$message = array();
		$message[] = '';
		$message[] = '---------------------------------------------------------------------';
		$message[] = '[' . $show . '] - ' . date('Y-m-d H:i:s');
		$message[] = '';
		$message[] = "$errorMessage in $file ($line)";
		$message[] = '';
		$message[] = '[Trace]';
		$message[] = $this->getBacktrace();
		$message[] = '---------------------------------------------------------------------';
		$message[] = '';

		$this->writeArrayToErrorLog($message);
	}

	/**
	 * Message ohne Backtrace!
	 *
	 * @param $message
	 */
	public function message($message) {

		$this->writeArrayToErrorLog(array($message));
	}

	/**
	 * @param $data
	 *
	 * @return null
	 */
	public function warn($data) {

		if (is_array($data)) {
			$data = print_r($data, true);
		}

		$message = array();
		$message[] = '';
		$message[] = '---------------------------------------------------------------------';
		$message[] = '[Warning message] - ' . date('Y-m-d H:i:s');
		$message[] = '';
		$message[] = $data;
		$message[] = '';
		$message[] = '[Trace]';
		$message[] = $this->getBacktrace();
		$message[] = '---------------------------------------------------------------------';
		$message[] = '';

		$this->writeArrayToErrorLog($message);
	}

	/**
	 * @param $data
	 *
	 * @return null
	 */
	public function debug($data) {

		if (false === $this->isDevelopmentMode()) {
			return null;
		}

		if (is_array($data)) {
			$data = print_r($data, true);
		}

		$message = array();
		$message[] = '';
		$message[] = '---------------------------------------------------------------------';
		$message[] = '[Debug message] - ' . date('Y-m-d H:i:s');
		$message[] = '';
		$message[] = $data;
		$message[] = '';
		$message[] = '[Trace]';
		$message[] = $this->getBacktrace();
		$message[] = '---------------------------------------------------------------------';
		$message[] = '';

		$this->writeArrayToErrorLog($message);
	}

	/**
	 * Returns the current backtrace.
	 *
	 * @return    string                        The current Backtrace as string
	 *
	 * @param $backtrace
	 */
	public function getBacktrace($backtrace = null) {

		static $className;

		$trace = array();

		if (null === $backtrace) {
			$backtrace = debug_backtrace();
		}

		if (null === $className) {
			$className = __CLASS__;
		}

		// generate backtrace array and strip entries from itself
		$j = 0;
		$c = count($backtrace) - 1;
		for ($i = $c; $i > 0; $i--) {
			if (isset($backtrace[$i]['class']) && $backtrace[$i]['class'] === $className) {
				continue;
			}
			$j++;
			if (false === isset($backtrace[$i]['file'])) {
				$backtrace[$i]['file'] = '';
			}

			if (false === isset($backtrace[$i]['line'])) {
				$backtrace[$i]['line'] = '';
			}

			if (false === isset($backtrace[$i]['type'])) {
				$backtrace[$i]['type'] = '';
			}

			if (false === isset($backtrace[$i]['class'])) {
				$backtrace[$i]['class'] = '';
			}

			if (false === isset($backtrace[$i]['function'])) {
				$backtrace[$i]['function'] = '';
			}

			$class = $backtrace[$i]['class'];
			$type = $backtrace[$i]['type'];
			$function = $backtrace[$i]['function'];
			$file = $backtrace[$i]['file'];
			$line = $backtrace[$i]['line'];
			$trace[] = "[#$j] {$class}{$type}{$function} in {$file} on line {$line}";
		}

		// return backtrace as string
		return implode("\n", $trace) . "\n";
	}

	/**
	 * Write into error log.
	 *
	 * @param array $data
	 */
	private function writeArrayToErrorLog(array $data) {

		foreach ($data as $msg) {
			error_log($msg . PHP_EOL, 3, $this->customLogFile);
			error_log($msg . PHP_EOL, 3, $this->streamLogFile);

			// php-ini error_log auch nehmen
			if (!defined('UNIT_TESTS')) {
				error_log($msg . PHP_EOL);
			}
		}
	}
}
