<?php
namespace Insphare\Base;

/**
 * Class BitwiseFlag
 */
abstract class BitwiseFlag {

	/**
	 * @var int
	 */
	private $flags = 0;

	/**
	 * @param int $flags
	 */
	public function initFlags($flags = 0) {
		$this->flags = (int)$flags;
	}

	/**
	 * @param int $flag
	 */
	public function addFlag($flag) {
		$this->assertValidBitFlag($flag);
		if (!$this->isFlagActive($flag)) {
			$this->flags ^= $flag;
		}
	}

	/**
	 * @param int $flag
	 * @return bool
	 */
	public function isFlagActive($flag) {
		$this->assertValidBitFlag($flag);
		return ($this->flags & $flag) === $flag;
	}

	/**
	 * @param int $flag
	 * @throws Exception
	 */
	private function assertValidBitFlag($flag) {
		if (!(0 === strrpos(decbin($flag), (string)1))) {
			throw new Exception('Integer-Flag: "' . $flag . '" is not an valid bitwise flag position. Only 1,2,4,8,16 etc.');
		}
	}

	/**
	 * @param int $flag
	 */
	public function removeFlag($flag) {
		$this->assertValidBitFlag($flag);

		if ($this->isFlagActive($flag)) {
			$this->flags ^= $flag;
		}
	}

	/**
	 * @param $flags
	 */
	public function removeFlagByArray(array $flags) {
		array_walk($flags, array($this, 'removeFlag'));
	}

	/**
	 * @param $flags
	 */
	public function addFlagByArray(array $flags) {
		array_walk($flags, array($this, 'addFlag'));
	}
}
