<?php

class BitwiseFlagTest extends PHPUnit_Framework_TestCase {

	const FLAG_ADMIN = 1;
	const FLAG_MOD = 2;
	const FLAG_GUIDE = 4;
	const FLAG_USER = 8;
	const FLAG_NEWBIE = 16;
	const FLAG_GUEST = 32;
	const FLAG_OTHERS = 64;
	const FLAG_YOUR_MOTHER = 128;

	/**
	 * @return PHPUnit_Framework_MockObject_MockObject|\Insphare\Base\BitwiseFlag
	 */
	private function getClass() {
		return $this->getMockForAbstractClass('Insphare\Base\BitwiseFlag');
	}

	public function testFlagActive() {
		$flagCollection = array(
			self::FLAG_ADMIN,
			self::FLAG_MOD,
			self::FLAG_GUIDE,
			self::FLAG_USER,
			self::FLAG_NEWBIE,
			self::FLAG_GUEST,
			self::FLAG_OTHERS,
			self::FLAG_YOUR_MOTHER,
		);
		$bitwiseFlag = $this->getClass();
		$bitwiseFlag->initFlags(array_sum($flagCollection));
		foreach ($flagCollection as $flag) {
			$this->assertTrue($bitwiseFlag->isFlagActive($flag));
		}

		$removeFlag = array(
			self::FLAG_GUEST,
			self::FLAG_OTHERS,
			self::FLAG_YOUR_MOTHER,
		);
		array_walk($removeFlag, array($bitwiseFlag, 'removeFlag'));

		$flagCollection = array(
			self::FLAG_ADMIN,
			self::FLAG_MOD,
			self::FLAG_GUIDE,
			self::FLAG_USER,
			self::FLAG_NEWBIE,
		);
		foreach ($flagCollection as $flag) {
			$this->assertTrue($bitwiseFlag->isFlagActive($flag));
		}


		$flagCollection = array(
			self::FLAG_GUEST,
			self::FLAG_OTHERS,
			self::FLAG_YOUR_MOTHER,
		);
		foreach ($flagCollection as $flag) {
			$this->assertFalse($bitwiseFlag->isFlagActive($flag));
		}
	}

	public function testDoubleRemoveAdd() {
		$flagCollection = array(
			self::FLAG_ADMIN,
			self::FLAG_MOD,
			self::FLAG_GUIDE,
			self::FLAG_GUEST,
			self::FLAG_OTHERS,
			self::FLAG_YOUR_MOTHER,
		);
		$bitwiseFlag = $this->getClass();
		$bitwiseFlag->addFlagByArray($flagCollection);
		$bitwiseFlag->addFlagByArray($flagCollection);
		foreach ($flagCollection as $flag) {
			$this->assertTrue($bitwiseFlag->isFlagActive($flag));
		}

		$bitwiseFlag->removeFlagByArray($flagCollection);
		$bitwiseFlag->removeFlagByArray($flagCollection);
		foreach ($flagCollection as $flag) {
			$this->assertFalse($bitwiseFlag->isFlagActive($flag));
		}
	}

	public function addRemoveFlagByArray() {
		$flagCollection = array(
			self::FLAG_ADMIN,
			self::FLAG_MOD,
			self::FLAG_GUIDE,
			self::FLAG_GUEST,
			self::FLAG_OTHERS,
			self::FLAG_YOUR_MOTHER,
		);
		$bitwiseFlag = $this->getClass();
		$bitwiseFlag->addFlagByArray($flagCollection);
		foreach ($flagCollection as $flag) {
			$this->assertTrue($bitwiseFlag->isFlagActive($flag));
		}

		$flagCollection = array(
			self::FLAG_USER,
			self::FLAG_NEWBIE,
		);
		foreach ($flagCollection as $flag) {
			$this->assertFalse($bitwiseFlag->isFlagActive($flag));
		}

		$flagCollectionRemove = array(
			self::FLAG_GUEST,
			self::FLAG_OTHERS,
			self::FLAG_YOUR_MOTHER,
		);

		$bitwiseFlag->removeFlagByArray($flagCollectionRemove);
		foreach ($flagCollection as $flag) {
			$this->assertFalse($bitwiseFlag->isFlagActive($flag));
		}

		$flagCollection = array(
			self::FLAG_USER,
			self::FLAG_NEWBIE,
		);
		foreach ($flagCollection as $flag) {
			$this->assertFalse($bitwiseFlag->isFlagActive($flag));
		}
	}

	 /**
	 * @dataProvider flagsProvider
	 */
	public function testWrongFlagIntegers($flagInt, $isRightFlag) {
		if (false === $isRightFlag) {
			$this->setExpectedException('\Insphare\Base\Exception');
		}
		$this->getClass()->addFlag($flagInt);
	}

	public function flagsProvider() {
		return array(
			array(0, false),
			array(1, true),
			array(2, true),
			array(3, false),
			array(4, true),
			array(5, false),
			array(6, false),
			array(7, false),
			array(8, true),
			array(64, true),
		);
	}



}
