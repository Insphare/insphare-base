<?php
namespace Insphare\Base\Cli;
use Insphare\Base\BitwiseFlag;

/**
 * String
 */
class String extends BitwiseFlag {

	const BOLD = 1;
	const DIM = 2;
	const UNDERLINED = 4;
	const BLINK = 5;
	const REVERSE = 7; // invert the foreground and background colors)
	const HIDDEN = 8; //useful for passwords
	const RESET = 0;

	const FLAG_BOLD = 1;
	const FLAG_DIM = 2;
	const FLAG_UNDERLINED = 4;
	const FLAG_REVERSE = 8;
	const FLAG_HIDDEN = 16;
	const FLAG_BLINK = 32;
	const FLAG_RESET = 64;
	const FLAG_BREAK_LINE = 128;

	private $useForegroundColor = null;
	private $useBackgroundColor = null;

	/**
	 * @var array
	 */
	private $foregroundColors = array(
		Formatter::COLOR_BLACK => '0;30',
		Formatter::COLOR_DARK_GREY => '1;30',
		Formatter::COLOR_BLUE => '0;34',
		Formatter::COLOR_LIGHT_BLUE => '1;34',
		Formatter::COLOR_GREEN => '0;32',
		Formatter::COLOR_LIGHT_GREEN => '1;32',
		Formatter::COLOR_CYAN => '0;36',
		Formatter::COLOR_LIGHT_CYAN => '1;36',
		Formatter::COLOR_RED => '0;31',
		Formatter::COLOR_LIGHT_RED => '1;31',
		Formatter::COLOR_PURPLE => '0;35',
		Formatter::COLOR_LIGHT_PURPLE => '1;35',
		Formatter::COLOR_LIGHT_YELLOW => '0;33',
		Formatter::COLOR_YELLOW => '1;33',
		Formatter::COLOR_LIGHT_GRAY => '0;37',
		Formatter::COLOR_WHITE => '1;37'
	);

	/**
	 * @var array
	 */
	private $backgroundColor = array(
		Formatter::COLOR_BLACK => '40',
		Formatter::COLOR_RED => '41',
		Formatter::COLOR_GREEN => '42',
		Formatter::COLOR_YELLOW => '43',
		Formatter::COLOR_BLUE => '44',
		Formatter::COLOR_MAGENTA => '45',
		Formatter::COLOR_CYAN => '46',
		Formatter::COLOR_LIGHT_GRAY => '47',
	);

	public function __construct($text) {
		$this->text = (string)$text;
	}

	public function addForegroundColor($colorName) {
		$this->useForegroundColor = $this->validateForegroundColor($colorName);
	}

	public function addBackgroundColor($colorName) {
		$this->useBackgroundColor = $this->validateBackgroundColor($colorName);
	}

	/**
	 * @param $colorName
	 *
	 * @return null
	 * @throws \InvalidArgumentException
	 */
	private function validateForegroundColor($colorName) {
		if (null == $colorName) {
			return null;
		}

		if (!isset($this->foregroundColors[$colorName])) {
			throw new \InvalidArgumentException('Unknown foreground color.');
		}

		return $this->foregroundColors[$colorName];
	}

	/**
	 * @param $colorName
	 *
	 * @return null
	 * @throws \InvalidArgumentException
	 */
	private function validateBackgroundColor($colorName) {
		if (null == $colorName) {
			return null;
		}
		if (!isset($this->backgroundColor[$colorName])) {
			throw new \InvalidArgumentException('Unknown background color.');
		}

		return $this->backgroundColor[$colorName];
	}

	private function setConsoleSpelling(array $flags, $text) {
		return "\033[" . implode(';', $flags) . "m".$text;
	}

	public function __toString() {

		$process = array(
			self::FLAG_BOLD => self::BOLD,
			self::FLAG_BLINK => self::BLINK,
			self::FLAG_UNDERLINED => self::UNDERLINED,
			self::FLAG_HIDDEN => self::HIDDEN,
			self::FLAG_DIM => self::DIM,
			self::FLAG_RESET => self::RESET,
			self::FLAG_REVERSE => self::REVERSE,
		);

		$addFlags = array();
		if (!is_null($this->useForegroundColor)) {
			$addFlags[$this->useForegroundColor] = $this->useForegroundColor;
		}
		if (!is_null($this->useBackgroundColor)) {
			$addFlags[$this->useBackgroundColor] = $this->useBackgroundColor;
		}

		foreach ($process as $bitFlag => $consoleSpelling) {
			if ($this->isFlagActive($bitFlag)) {
				$addFlags[$consoleSpelling]=$consoleSpelling;
			}
		}

		if (!empty($addFlags)) {
			$this->text = "{$this->setConsoleSpelling($addFlags, $this->text)}";
		}

		$this->text .= $this->setConsoleSpelling(array(self::RESET), '');

		if ($this->isFlagActive(self::FLAG_BREAK_LINE)) {
			$this->text .= PHP_EOL;
		}

		return $this->text;
	}

}
