<?php
namespace Insphare\Base\Application;
use Insphare\Base\DirectoryIterator;
use Insphare\Base\EnvironmentVars;
use Insphare\Base\Exception;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Setup
 *
 * @package Insphare\Base
 */
class Setup {

	/**
	 * @var array
	 */
	private $configDirs = array();

	/**
	 * @var array
	 */
	private $entityPath = array();

	/**
	 * @var array
	 */
	private $listenerPath = array();

	/**
	 * @var string
	 */
	private $proxyPath = '';

	private static $isAlreadyRunning = false;

	/**
	 *
	 */
	public function __construct() {
		if (true === self::$isAlreadyRunning) {
			throw new Exception('Please do not call this setup class manually! Please use the ObjectContainer->getSetup()');
		}

		self::$isAlreadyRunning = 1;
	}

	/**
	 * Add include path for more config directories and append or overwrite our variables in our known config environment.
	 *  @param string $configPath
	 */
	public function addCustomConfig($configPath) {
		$this->configDirs[] = (string)$configPath;
	}

	/**
	 * @param string $listenerPath
	 */
	public function addListenerPath($listenerPath) {
		$this->listenerPath[] = (string)$listenerPath;
	}

	/**
	 * @param string $entityPath
	 */
	public function addEntityPath($entityPath) {
		$this->entityPath[] = rtrim((string)$entityPath, DIRECTORY_SEPARATOR);
	}

	/**
	 * @param string $proxyPath
	 */
	public function setProxyPath($proxyPath) {
		$this->proxyPath = (string)$proxyPath;
	}

	private function loadConfig() {
		$envConfig = array();
		foreach ($this->configDirs as $dir) {
			$fileSpl = new DirectoryIterator($dir);
			$fileSpl->addAllowedExtension('yml');
			foreach ($fileSpl->getSplFiles() as $splFile) {
				$config = Yaml::parse(file_get_contents((string)$splFile));
				$envConfig = array_replace_recursive($envConfig, (array)$config);
			}
		}

		return $envConfig;
	}

	public function addConfigDirectory($directoryPath) {
		$this->configDirs[] = $directoryPath;
		$config = $this->loadConfig();
		$this->writeToEnvironment($config);

	}

	private function writeToEnvironment(array $config) {
		foreach ($config as $key => $value) {
			EnvironmentVars::set($key, $value);
		}
	}

	public function addConfigValue($array) {

	}

	/**
	 *
	 */
	public function run() {
		if (count($this->configDirs) === 1) {
			throw new Exception('You have to register your config path! Use Setup->addCustomConfig()');
		}

		$envConfig = $this->loadConfig();

		$overWriteEntityPath = array('doctrine.path' => array('entities' => $this->entityPath));
		if (!empty($this->proxyPath)) {
			$overWriteEntityPath['doctrine.path']['proxy'] = $this->proxyPath;
		}

		if (!empty($this->listenerPath)) {
			$overWriteEntityPath['doctrine.listenerPath'] = $this->listenerPath;
		}

		$envConfig = array_replace_recursive($envConfig, $overWriteEntityPath);
		$this->writeToEnvironment($envConfig);
	}
}
