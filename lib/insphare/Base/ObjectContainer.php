<?php
namespace Insphare\Base;

/**
 * The following objects have to be assigned with fully qualified namespace!
 *
 * Core_ObjectContainer
 *
 * @method Insphare\Base\Application\Setup getSetup
 * @method Insphare\Base\Application\Setup setSetup
 */
class ObjectContainer {

	/**
	 * @var array
	 */
	private static $mappingMethodInstance = array();

	/**
	 * @var array
	 */
	private static $objects = array();

	/**
	 * @param string$methodName
	 * @param array $arguments
	 * @return mixed
	 * @throws Exception
	 */
	public function __call($methodName, $arguments) {
		$mapping = $this->getMethodInstanceMapping();

		if (!isset($mapping[$methodName])) {
			throw new Exception('Unknown method: ' . $methodName);
		}

		$instance = $mapping[$methodName];

		switch (substr($methodName, 0, 3)) {
			case 'set':
				$originObject = current($arguments);
				if (!$originObject instanceof $instance) {
					throw new Exception('The given object have to be instance of (' . $instance . ')');
				}
				self::$objects[$instance] = $originObject;
				break;

			case 'get':
				if (!isset(self::$objects[$instance])) {
					throw new Exception('Instance ' . $instance . ' have to be set before.');
				}
				return self::$objects[$instance];
		}

		return null;
	}

	/**
	 * @return array
	 */
	private function getMethodInstanceMapping() {
		if (empty(self::$mappingMethodInstance)) {
			$reflection = new \ReflectionClass($this);
			$classDocComment = $reflection->getDocComment();
			$regEx = '~@method\s+(?<instanceName>[^\s]+)\s+(?<methodName>[^\$\s]+)~is';
			preg_match_all($regEx, $classDocComment, $arrMatch, PREG_SET_ORDER);
			foreach ($arrMatch as $match) {
				$methodName = $match['methodName'];
				$instance = $match['instanceName'];
				self::$mappingMethodInstance[$methodName] = $instance;
			}
		}

		return self::$mappingMethodInstance;
	}
}
