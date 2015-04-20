<?php
namespace Insphare\Base;

class DirectoryIterator {

	/**
	 * @var string
	 */
	private $destination = '';

	/**
	 * @var bool
	 */
	private $recursive = false;

	/**
	 * @var array
	 */
	private $allowedExtensions = array();

	/**
	 * @var array
	 */
	private $files = array();

	/**
	 * @var array
	 */
	private $excluded = array();

	/**
	 * @var array
	 */
	private $directories = array();

	/**
	 * @param $destination
	 *
	 * @throws Exception
	 */
	public function __construct($destination) {

		if (!file_exists($destination)) {
			throw new Exception('File-Path-Destination does not exists. ' . $destination);
		}

		$this->destination = $destination;
	}

	/**
	 * @return string
	 */
	public function getDestination() {
		return $this->destination;
	}

	/**
	 * @param bool $state
	 *
	 * @return $this
	 */
	public function setRecursive($state) {
		$this->recursive = (bool)$state;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getRecursive() {
		return $this->recursive;
	}

	/**
	 * @param $extensionName
	 * @return $this
	 */
	public function addAllowedExtension($extensionName) {
		$extensionName = strtolower((string)$extensionName);
		$this->allowedExtensions[$extensionName] = $extensionName;
		return $this;
	}

	/**
	 * @param $dir
	 * @param bool $asSplFile
	 */
	protected function read($dir, $asSplFile = false) {
		$iterator = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);

		/**
		 * @var $fileInfo \SplFileInfo
		 */
		foreach ($iterator as $fileInfo) {
			if (true === $fileInfo->isDir()) {
				$realPath = $fileInfo->getRealPath();
				if (false === $this->isExcluded($realPath)) {
					$this->directories[$realPath] = $realPath;
				}
			}

			if (true === $fileInfo->isFile()) {
				$fileName = $fileInfo->getRealPath();

				if (count($this->allowedExtensions)) {
					$extension = strtolower(pathinfo($fileInfo->getFilename(), PATHINFO_EXTENSION));
					if (!isset($this->allowedExtensions[$extension])) {
						continue;
					}
				}

				if (true === $this->isExcluded($fileName)) {
					continue;
				}

				if (true === $asSplFile) {
					$this->files[spl_object_hash($fileInfo)] = $fileInfo;
				}
				else {
					$this->files[$fileName] = $fileName;
				}
			}

			if (true === $fileInfo->isDir() && true === $this->getRecursive()) {
				$this->read($fileInfo->getPath() . DIRECTORY_SEPARATOR . $fileInfo->getFilename());
			}
		}
	}

	/**
	 * @param $string
	 * @return bool
	 */
	private function isExcluded($string) {
		if (count($this->excluded)) {
			foreach ($this->excluded as $regex) {
				if (preg_match($regex, $string)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function getDirectories() {
		if (empty($this->files)) {
			$this->getFiles();
		}
		return $this->directories;
	}

	/**
	 * @param $regEx
	 */
	public function addExclusion($regEx) {
		$this->excluded[] = '~' . $regEx . '~i';
	}

	/**
	 * Return files names
	 *
	 * @return array
	 */
	public function getFiles() {
		$this->read($this->destination);
		return $this->files;
	}

	/**
	 * @return array
	 */
	public function getSplFiles() {
		$this->read($this->destination, true);
		return $this->files;
	}
}

