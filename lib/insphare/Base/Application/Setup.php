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
	 * @var bool
	 */
	private $isRunning = false;
	/**
	 * @var array
	 */
	private $configDirs = array();

	/**
	 * @var array
	 */
	private $entityPath = array();

	/**
	 * @var string
	 */
	private $proxyPath = '';

	/**
	 *
	 */
	public function __construct() {
		$path = array_slice(explode(DIRECTORY_SEPARATOR, __DIR__), 0, -4);
		$path[] = 'base-config';
		$this->configDirs[] = implode(DIRECTORY_SEPARATOR, $path);
	}

	/**
	 * Add include path for more config directories and append or overwrite our variables in our known config environment.
	 *  @param string $configPath
	 */
	public function addCustomConfig($configPath) {
		$this->configDirs[] = (string)$configPath;
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

	/**
	 *
	 */
	public function run() {
		if (true === $this->isRunning) {
			throw new Exception('The insphare/orm application is already running.');
		}

		if (count($this->configDirs) === 1) {
			throw new Exception('You have to register your config path! Use Setup->addCustomConfig()');
		}

		if (!count($this->entityPath)) {
			throw new Exception('You have to register your path to your entities! Use Setup->addEntityPath()');
		}

		$envConfig = array();
		foreach ($this->configDirs as $dir) {
			$fileSpl = new DirectoryIterator($dir);
			$fileSpl->addAllowedExtension('yml');
			foreach ($fileSpl->getSplFiles() as $splFile) {
				$config = Yaml::parse(file_get_contents((string)$splFile));
				$envConfig = array_replace_recursive($envConfig, (array)$config);
			}
		}

		$overWriteEntityPath = array('doctrine.path' => array('entities' => $this->entityPath));
		if (!empty($this->proxyPath)) {
			$overWriteEntityPath['doctrine.path']['proxy'] = $this->proxyPath;
		}

		$envConfig = array_replace_recursive($envConfig, $overWriteEntityPath);
		foreach ($envConfig as $key => $value) {
			EnvironmentVars::set($key, $value);
		}
		$this->isRunning = true;
	}
}
