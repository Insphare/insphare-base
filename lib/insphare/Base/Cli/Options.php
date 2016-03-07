<?php
namespace Insphare\Base\Cli;


use Insphare\Base\Exception;

class Options {

	/**
	 * Required option flag.
	 *
	 * @var int
	 */
	const OPTION_TYPE_REQUIRED = 1;

	/**
	 * Optional option flag.
	 *
	 * @var int
	 */
	const OPTION_TYPE_OPTIONAL = 2;

	/**
	 * No value option flag.
	 *
	 * @var int
	 */
	const OPTION_TYPE_NO_VALUE = 3;

	/**
	 * Contains all given option types.
	 * E.g. for validation of type.
	 *
	 * @var array
	 */
	private $optionTypes = array(
		self::OPTION_TYPE_NO_VALUE => 'Optional',
		self::OPTION_TYPE_OPTIONAL => 'Optional',
		self::OPTION_TYPE_REQUIRED => 'Required',
	);

	/**
	 * Short options.
	 * Key: Word
	 *
	 * @var Option[]
	 */
	private $configShortOptions = array();

	/**
	 * Long options.
	 * Key: Word
	 *
	 * @var Option[]
	 */
	private $configLongOptions = array();

	/**
	 * Contains all given options.
	 *
	 * @var array
	 */
	private $givenOptions = array();

	/**
	 * File for help.
	 *
	 * @var string
	 */
	private $file;

	/**
	 * @var array
	 */
	private $cachedOptionForBetterPerformance = array();

	/**
	 * @param $file
	 * @throws Exception
	 */
	public function __construct($file) {

		if (strtolower(PHP_SAPI) != 'cli') {
			throw new Exception('This script run not as cli!');
		}

		$this->file = basename($file);
		$strFullHelp = 'Print help with all options.';
		$this->addLongOption('help', self::OPTION_TYPE_NO_VALUE, 'Print Help', $strFullHelp);
	}

	/**
	 * @param $key
	 *
	 * @return bool
	 */
	protected function isSetOption($key) {
		return isset($this->givenOptions[$key]);
	}

	/**
	 * @param $key
	 *
	 * @return null
	 */
	public function getOptionValue($key) {
		if (false === $this->isSetOption($key)) {
			return null;
		}

		return $this->givenOptions[$key];
	}

	/**
	 * @param $optionType
	 *
	 * @return string
	 * @throws Exception
	 */
	private function getSignByOptionType($optionType) {
		switch ($optionType) {
			case self::OPTION_TYPE_REQUIRED:
				$sign = ':';
				break;

			case self::OPTION_TYPE_OPTIONAL:
				$sign = '::';
				break;

			case self::OPTION_TYPE_NO_VALUE:
				$sign = '';
				break;

			default:
				throw new Exception('Unkown option value.');
		}

		return $sign;
	}

	/**
	 * @return string
	 */
	private function getShortOptionsString() {
		$strShortOpts = '';
		foreach ($this->configShortOptions as $option => $config) {
			$sign = $this->getSignByOptionType($config->getType());
			$strShortOpts .= $option . $sign;
		}

		return $strShortOpts;
	}

	/**
	 * @return array
	 */
	private function getLongOptionsArray() {
		$arrLongOpts = array();
		foreach ($this->configLongOptions as $option => $config) {
			$sign = $this->getSignByOptionType($config->getType());
			$arrLongOpts[] = $option . $sign;
		}

		return $arrLongOpts;
	}

	/**
	 * @param $type
	 *
	 * @throws Exception
	 */
	private function validateOptionType($type) {
		if (!isset($this->optionTypes[$type])) {
			throw new Exception('Unknown option type: ' . $type);
		}
	}

	/**
	 * @param $type
	 *
	 * @return string
	 */
	private function getOptionTypeAsString($type) {
		return $this->optionTypes[$type];
	}

	/**
	 * Adds a short option.
	 *
	 * @param $key
	 * @param $optionType
	 * @param $shortDescription
	 * @param $longDescription
	 * @param null $defaultValue
	 *
	 * @throws Exception
	 */
	public function addShortOption($key, $optionType, $shortDescription, $longDescription, $defaultValue = null) {
		$key = trim($key);

		if (empty($key)) {
			throw new Exception('No key given!');
		}

		if (strlen($key) !== 1) {
			throw new Exception('Your key can only have one char!');
		}

		if (isset($this->configShortOptions[$key])) {
			throw new Exception('The short option key: ' . $key . ' is already in use!');
		}

		$this->validateOptionType($optionType);

		$option = new Option();
		$option->setKey($key);
		$option->setType($optionType);
		$option->setLongDescription($longDescription);
		$option->setShortDescription($shortDescription);
		$option->setDefaultValue($defaultValue);

		$this->configShortOptions[$key] = $option;
	}

	/**
	 * Adds a long option.
	 *
	 * @param $key
	 * @param $optionType
	 * @param $shortDescription
	 * @param $longDescription
	 * @param null $defaultValue
	 *
	 * @throws Exception
	 */
	public function addLongOption($key, $optionType, $shortDescription, $longDescription, $defaultValue = null) {
		$key = trim($key);

		if (empty($key)) {
			throw new Exception('No key given!');
		}

		if (isset($this->configLongOptions[$key])) {
			throw new Exception('The long option key: ' . $key . ' is already in use!');
		}

		$this->validateOptionType($optionType);

		$option = new Option();
		$option->setKey($key);
		$option->setType($optionType);
		$option->setLongDescription($longDescription);
		$option->setShortDescription($shortDescription);
		$option->setDefaultValue($defaultValue);

		$this->configLongOptions[$key] = $option;
	}

	/**
	 * @return Option[]
	 */
	private function getAllConfigOptions() {
		if (empty($this->cachedOptionForBetterPerformance)) {
			$options = array();
			$options += $this->configShortOptions;
			$options += $this->configLongOptions;
			$this->cachedOptionForBetterPerformance = $options;
		}

		return $this->cachedOptionForBetterPerformance;
	}

	/**
	 * @param $key
	 *
	 * @return Option
	 */
	private function getConfigByKey($key) {
		$configs = $this->getAllConfigOptions();

		return $configs[$key];
	}

	/**
	 * Determine all given params.
	 */
	public function parse() {
		$strShortOpts = $this->getShortOptionsString();
		$arrLongOpts = $this->getLongOptionsArray();
		$opts = getopt(trim($strShortOpts), $arrLongOpts);

		// hotfix that: php css.php -a="Web" -l="blackrockdigital" -t="main"
		// because, it have more options given as defined... result is that:
		//array(1) {
		//  'a' =>
		//  array(3) {
		//    [0] =>
		//    string(3) "Web"
		//    [1] =>
		//    string(13) "ckrockdigital"
		//    [2] =>
		//    string(2) "in"
		//  }
		//}
		// and should convert into ['a' => 'Web']
		$arrayKeys = (is_array($opts) ? array_keys($opts) : []);
		if (is_array($opts) && count($opts) === 1 && !empty($opts[current($arrayKeys)]) && is_array($opts['a']) && $arrayKeys[0] === 'a') {
			$opts = ['a' => current($opts[current($arrayKeys)])];
		}

		foreach ($opts as $strOptionName => $strOptionValue) {
			$config = $this->getConfigByKey($strOptionName);

			/**
			 * Fallback: if the no_value option is set, this value should set to true.
			 */
			if ($config->getType() === self::OPTION_TYPE_NO_VALUE) {
				$strOptionValue = true;
			}

			$this->givenOptions[$strOptionName] = $strOptionValue;
		}

		if ($this->isSetOption('help')) {
			$this->printHelp();
		}

		// validate required params
		$options = $this->getAllConfigOptions();
		foreach ($options as $key => $config) {
			if ($config->getType() !== self::OPTION_TYPE_REQUIRED) {
				// check there is not given options and have default values.
				$optional = $config->getDefaultValue();

				if (false === $this->isSetOption($key) && $optional != null) {
					$this->givenOptions[$key] = $optional;
				}

				continue;
			}

			$optionValue = $this->getOptionValue($key);
			if (empty($optionValue)) {
				$this->printHelp('Missing required param [' . $key . '].');
			}
		}
	}

	/**
	 * @param Option $config
	 * @param string $prefix
	 *
	 * @return string
	 */
	private function getHelpLongLine(Option $config, $prefix = '-') {
		$temp = $prefix . "%s\t%s\t%s\t%s %s";

		$default = '';
		if (null !== $config->getDefaultValue()) {
			$default = '[Default: ' . $config->getDefaultValue() . ']';
		}

		return sprintf(
			$temp,
			$this->padRight($config->getKey(), 20),
			$this->padRight('[' . $this->getOptionTypeAsString($config->getType()) . ']', 10),
			$this->padRight($config->getShortDescription(), 20),
			$this->padRight(ucfirst($config->getLongDescription()), 40),
			$this->padRight($default, 20)
		);
	}

	/**
	 * @param $string
	 * @param $length
	 *
	 * @return string
	 */
	private function padRight($string, $length) {
		return str_pad($string, $length, ' ', STR_PAD_RIGHT);
	}

	/**
	 * @param null $text
	 */
	public function printHelp($text = null) {
		$descriptionShort = array();
		$descriptionLong = array();

		$output = new Output();
		$output->createText('')->addBreakLine();

		if (null !== $text) {
			$output->createText($text)->addBreakLine()->addForegroundColor(Formatter::COLOR_LIGHT_RED);
		}

		$opts = array();
		foreach ($this->configShortOptions as $key => $config) {
			$value = $config->getShortDescription();
			$opts[] = '-' . $key . '[' . $value . ']';
			$descriptionShort[] = $this->getHelpLongLine($config);
		}

		foreach ($this->configLongOptions as $key => $config) {
			if ($key !== 'help') {
				$value = $config->getShortDescription();
				$opts[] = '--' . $key . '="[' . $value . ']"';
			}

			$descriptionLong[] = $this->getHelpLongLine($config, '--');
		}

		$output->createText('')->addBreakLine();
		$output->createText('Usage: php ' . $this->file . ' ' . implode(' ', $opts))->addBreakLine()->addForegroundColor(Formatter::COLOR_GREEN)->addBold();

		if (count($descriptionShort)) {
			$output->createText('')->addBreakLine();
			$output->createText('Short options:')->addBreakLine()->addForegroundColor(Formatter::COLOR_LIGHT_YELLOW);
			foreach ($descriptionShort as $desc) {
				$output->createText($desc)->addBreakLine()->addDim();
			}
		}

		if (count($descriptionLong)) {
			$output->createText('')->addBreakLine();
			$output->createText('Long options:')->addBreakLine()->addForegroundColor(Formatter::COLOR_LIGHT_YELLOW);
			foreach ($descriptionLong as $desc) {
				$output->createText($desc)->addBreakLine()->addDim();
			}
		}

		$output->createText('')->addBreakLine();
		$output->createText('')->addBreakLine();
		$output->flush();
		die();
	}
}
