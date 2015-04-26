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
		$this->includePath[] = rtrim(str_replace(array('/', '\\'), self::DS, $path), self::DS);
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
		if (!is_null($this->nameSpace) && 0 === strpos($className, $this->nameSpace . self::NS_CHAR)) {
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
				if ($this->checkLoadClass($file)) {
					break;
				}
			}
		}
		elseif (is_null($this->nameSpace)) {
			foreach ($this->includePath as $includePath) {
				$file = $includePath . self::DS . str_replace(array(
						'_',
						self::DS . self::DS
					), array(
						self::DS,
						self::DS
					), $className) . '.php';
				if ($this->checkLoadClass($file)) {
					break;
				}
			}
		}
	}

	/**
	 * @param string $file
	 * @return bool|null
	 */
	private function checkLoadClass($file) {
		if (file_exists($file)) {
			include_once $file;
			return true;
		}

		return null;
	}
}
