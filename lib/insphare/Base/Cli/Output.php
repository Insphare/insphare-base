<?php
namespace Insphare\Base\Cli;
use Insphare\Base\ArrayAccess;

/**
 * Class Output
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Output {

	/**
	 *
	 */
	public function __construct() {
		$this->output = new ArrayAccess();
	}

	/**
	 * @param $text
	 * @return Formatter
	 */
	public function createText($text) {
		$co = new Formatter($text);
		$this->output->append($co);

		return $co;
	}

	/**
	 * @return $this
	 */
	public function addBlankLine() {
		$this->createText('')->addBreakLine();
		return $this;
	}

	/**
	 *
	 */
	public function flush() {
		if (!$this->output->isEmpty()) {
			foreach ($this->output as &$item) {
				$item = (string)$item;
			}
			echo implode('', $this->output->getArrayCopy());
		}

		$this->reset();

		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		if (!$this->output->isEmpty()) {
			foreach ($this->output as &$item) {
				$item = (string)$item;
			}
		}

		$return = implode('', $this->output->getArrayCopy());
		$this->reset();

		return $return;
	}

	/**
	 *
	 */
	public function reset() {
		$this->output = new ArrayAccess();
	}

	/**
	 *
	 */
	public function __destruct() {
		$this->flush();
	}
}
