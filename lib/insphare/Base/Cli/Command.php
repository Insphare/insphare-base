<?php
namespace Insphare\Base\Cli;
use Insphare\Base\Exception;

/**
 * Class Command
 */
abstract class Command {

	/**
	 * @var Options
	 */
	private $options;

	/**
	 * @var Output
	 */
	private $output;

	/**
	 * @var Prompt
	 */
	private $prompt;

	/**
	 * @var Console
	 */
	private $console;

	/**
	 * @return mixed
	 */
	abstract protected function config();

	/**
	 * @return mixed
	 */
	abstract protected function process();

	/**
	 *
	 */
	public function __construct() {
		$this->options = new Options(__FILE__);
		$this->output = new Output();
		$this->prompt = new Prompt();
		$this->console = new Console(true);
	}

	/**
	 * @param string $paramName
	 * @param bool $isRequired
	 * @param null $shortDesc
	 * @param null $longDesc
	 * @param null $defaultValue
	 * @param null $useType
	 */
	protected function addShortOption($paramName, $isRequired = true, $shortDesc = null, $longDesc = null, $defaultValue = null, $useType = null) {
		$this->setOption(false, $paramName, $isRequired, $shortDesc, $longDesc, $defaultValue, $useType);
	}

	/**
	 * @param string $paramName
	 * @param bool $isRequired
	 * @param null $shortDesc
	 * @param null $longDesc
	 * @param null $defaultValue
	 * @param null $useType
	 */
	protected function addLongOption($paramName, $isRequired = true, $shortDesc = null, $longDesc = null, $defaultValue = null, $useType = null) {
		$this->setOption(true, $paramName, $isRequired, $shortDesc, $longDesc, $defaultValue, $useType);
	}

	/**
	 * @param bool $isLong
	 * @param string $paramName
	 * @param bool $isRequired
	 * @param null $shortDesc
	 * @param null $longDesc
	 * @param null $defaultValue
	 * @param null $useType
	 * @throws Exception
	 */
	private function setOption($isLong, $paramName, $isRequired = true, $shortDesc = null, $longDesc = null, $defaultValue = null, $useType = null) {
		$type = Options::OPTION_TYPE_OPTIONAL;
		if ($isRequired) {
			$type = Options::OPTION_TYPE_REQUIRED;
		}
		if (!is_null($useType)) {
			$type = $useType;
		}

		if (true === $isLong) {
			$this->options->addLongOption($paramName, $type, $shortDesc, $longDesc, $defaultValue);
		}
		else {
			$this->options->addShortOption($paramName, $type, $shortDesc, $longDesc, $defaultValue);
		}
	}

	/**
	 * @return Options
	 */
	protected function getOptions() {
		return $this->options;
	}

	/**
	 * @return Output
	 */
	protected function getOutput() {
		return $this->output;
	}

	/**
	 * @return Prompt
	 */
	protected function getPrompt() {
		return $this->prompt;
	}

	/**
	 * @return Console
	 */
	public function getConsole() {
		return $this->console;
	}

	/**
	 * @param $message
	 */
	protected function successMessage($message) {
		$this->getOutput()->createText($message)->addForegroundColor(Formatter::COLOR_GREEN);
		$this->getOutput()->flush();
	}

	/**
	 * @param $message
	 */
	protected function errorMessage($message) {
		$this->getOutput()->addBlankLine()->createText($message)->addBackgroundColor(Formatter::COLOR_RED);
		$this->getOutput()->flush();
	}

	/**
	 * @param $message
	 */
	protected function createSimpleTextAndFlush($message) {
		$this->getOutput()->createText($message)->addForegroundColor(Formatter::COLOR_LIGHT_GREEN)->addDim();
		$this->flushOutput();
	}

	/**
	 * @param $message
	 */
	protected function createSimpleLineBreakTextAndFlush($message) {
		$this->createSimpleTextAndFlush($message);
		$this->flushOutput();
		$this->getOutput()->addBlankLine()->flush();
	}

	/**
	 *
	 */
	protected function flushOutput() {
		$this->getOutput()->flush();
	}

	protected function executeConsoleCommand($command, $printResult = true) {
		$this->getOutput()->addBlankLine()->createText('Executing command: ')->addForegroundColor(Formatter::COLOR_YELLOW);
		$this->getOutput()->createText($command)->addBold()->addBreakLine()->addUnderlined();
		$this->getOutput()->addBlankLine();
		$this->flushOutput();
		$output = $this->getConsole()->addCommand($command)->execute();
		if (true === $printResult && PHP_SAPI === 'cli') {
			echo $output;
		}
	}

	/**
	 *
	 */
	public function execute() {
		$this->config();
		$this->getOptions()->parse();
		$this->process();
	}

	/**
	 *
	 */
	public function __destruct() {
		$this->getOutput()->addBlankLine()->addBlankLine()->flush();
	}
}
