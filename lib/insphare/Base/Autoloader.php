<?php
namespace Insphare\Base;

/**
 * Class Autoloader
 * @package Insphare\Base
 */
class Autoloader {

	/**
	 *
	 */
	const DS = DIRECTORY_SEPARATOR;
	/**
	 *
	 */
	const NS_CHAR = '\\';

	/**
	 * @var null
	 */
	private $nameSpace = null;

	private $includePath = array();

	/**
	 * @param null $nameSpace
	 */
	public function setNameSpace($nameSpace) {
		$this->nameSpace = $nameSpace;
	}

	/**
	 * @param $path
	 */
	public function addIncludePath($path) {
		$this->includePath[] = rtrim($path, self::DS);
	}

	/**
	 *
	 */
	public function register() {
		spl_autoload_register(array(
			$this,
			'autoload'
		));
	}

	/**
	 *
	 */
	public function unregister() {
		spl_autoload_unregister(array(
			$this,
			'autoload'
		));
	}

	/**
	 * @param $className
	 */
	private function autoload($className) {
		if (0 === strpos($className, $this->nameSpace . self::NS_CHAR)) {
			foreach ($this->includePath as $includePath) {
				$file = str_replace(array(
						'\\',
						$this->nameSpace,
						self::DS . self::DS
					), array(
						self::DS,
						$includePath,
						self::DS
					), $className) . '.php';
				if (file_exists($file)) {
					include_once $file;
					break;
				}
			}
		}
	}
}
