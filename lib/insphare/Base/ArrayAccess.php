<?php
namespace Insphare\Base;

class ArrayAccess extends \ArrayIterator  {

	/**
	 * @param array $array
	 * @param int $flags
	 */
	public function __construct($array = array(), $flags = 0) {
		parent::__construct($array, $flags);
	}

	/**
	 * @return bool
	 */
	public function isEmpty() {
		$count = $this->count();
		return empty($count);
	}

	/**
	 * @return mixed
	 */
	public function getFirst() {
		$this->rewind();
		return $this->current();
	}

	/**
	 * @return mixed|null
	 */
	public function getLast() {
		$data = array_keys($this->getArrayCopy());
		$key = end($data);
		if (empty($key)) {
			return null;
		}

		return $this->offsetGet($key);
	}

	/**
	 * @return array
	 */
	public function getArrayKeyCopy() {
		return array_keys($this->getArrayCopy());
	}
}
