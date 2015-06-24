<?php

use Insphare\Base\ShutdownScheduler;

$phpUnitValue = 5;

class ShutdownSchedulerTest extends PHPUnit_Framework_TestCase {

	public function testWrapper() {
		global $phpUnitValue;
		ShutdownScheduler::getInstance()->registerWrapper('unittest_wrapper');
		$this->assertEquals(5, $phpUnitValue);
		ShutdownScheduler::getInstance()->callRegisteredShutdown();
		$this->assertEquals(6, $phpUnitValue);
	}
}

if (!function_exists('unittest_wrapper')) {
	function unittest_wrapper() {
		global $phpUnitValue;
		$phpUnitValue++;
	}
}
