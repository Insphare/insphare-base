<?php

use Insphare\Base\ArrayAccess;

class ArrayAccessTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ArrayAccess
	 */
	private $arrayAccess;

	/**
	 * @return array
	 */
	private function getData() {
		$data = array_fill(0, 20, '');
		foreach ($data as $k => &$v) {
			$v = $k;
		}
		return $data;
	}

	public function setUp() {
		$this->arrayAccess = new ArrayAccess($this->getData());
	}

	public function testInstance() {
		$this->assertTrue($this->arrayAccess instanceof ArrayIterator);
		$this->assertTrue($this->arrayAccess instanceof ArrayAccess);
	}

	public function testContents() {
		$this->assertEquals(0, $this->arrayAccess->getFirst());
		$this->assertEquals(19, $this->arrayAccess->getLast());
		$this->assertFalse($this->arrayAccess->isEmpty());
		$this->assertSame(20, $this->arrayAccess->count());
		$this->assertSame($this->getData(), $this->arrayAccess->getArrayCopy());
		foreach ($this->getData() as $key => $value) {
			$copyContents = $this->arrayAccess->getArrayCopy();
			// schlüssel vergleichen
			$this->assertSame($key, $copyContents[$key]);
			$this->assertSame($value, $this->arrayAccess->offsetGet($key));
		}

		$this->assertSame(array_keys($this->getData()), $this->arrayAccess->getArrayKeyCopy());

		$this->arrayAccess->append('hui');
		$this->assertSame(21, $this->arrayAccess->count());
	}
}
