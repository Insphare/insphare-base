<?php
namespace Insphare\Base\Cli;

class Formatter {

	const COLOR_BLACK = 'black';
	const COLOR_DARK_GREY = 'dark_gray';
	const COLOR_BLUE = 'blue';
	const COLOR_LIGHT_BLUE = 'light_blue';
	const COLOR_GREEN = 'green';
	const COLOR_LIGHT_GREEN = 'light_green';
	const COLOR_CYAN = 'cyan';
	const COLOR_LIGHT_CYAN = 'light_cyan';
	const COLOR_RED = 'red';
	const COLOR_LIGHT_RED = 'light_red';
	const COLOR_PURPLE = 'purple';
	const COLOR_LIGHT_PURPLE = 'light_purple';
	const COLOR_LIGHT_YELLOW = 'light_yellow';
	const COLOR_YELLOW = 'yellow';
	const COLOR_LIGHT_GRAY = 'light_gray';
	const COLOR_WHITE = 'white';
	const COLOR_MAGENTA = 'magenta';

	/**
	 * @var String
	 */
	private $container;

	/**
	 * @param $string
	 */
	public function __construct($string) {
		$this->container = new Strings((string)$string);
	}

	/**
	 * @return $this
	 */
	public function addBreakLine() {
		$this->container->addFlag(Strings::FLAG_BREAK_LINE);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addBlink() {
		$this->container->addFlag(Strings::FLAG_BLINK);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addDim() {
		$this->container->addFlag(Strings::FLAG_DIM);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addHidden() {
		$this->container->addFlag(Strings::FLAG_HIDDEN);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addReverse() {
		$this->container->addFlag(Strings::FLAG_REVERSE);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addReset() {
		$this->container->addFlag(Strings::FLAG_RESET);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addUnderlined() {
		$this->container->addFlag(Strings::FLAG_UNDERLINED);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addBold() {
		$this->container->addFlag(Strings::FLAG_BOLD);
		return $this;
	}

	/**
	 * @param $color
	 * @return $this
	 */
	public function addForegroundColor($color) {
		$this->container->addForegroundColor($color);
		return $this;
	}

	/**
	 * @param $color
	 * @return $this
	 */
	public function addBackgroundColor($color) {
		$this->container->addBackgroundColor($color);
		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->container->__toString();
	}
}
