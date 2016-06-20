<?php
namespace Insphare\Base;
use Insphare\Base\ShutdownScheduler\Callback;

/**
 * Class ShutdownScheduler
 *
 * A lot of useful services may be delegated to this useful trigger.
 * It is very effective because it is executed at the end of the script but before any object destruction,
 * so all instantiations are still alive.
 * Here's a simple shutdown events manager class which allows to manage
 * either functions or static/dynamic methods, with an indefinite number of arguments availing on a internal handling
 * through call_user_func_array() specific functions.
 */
class ShutdownScheduler {

	/**
	 * Array to store user callbacks.
	 *
	 * @var \ArrayIterator
	 */
	private $callbacks;

	/**
	 * @var null|ShutdownScheduler
	 */
	private static $instance = null;

	/**
	 * The constructor is only accessible through the getInstance method.
	 */
	private function __construct() {
		$this->callbacks = new \ArrayIterator();

		$arguments = array(
			$this,
			'callRegisteredShutdown'
		);

		register_shutdown_function($arguments);
	}

	/**
	 * Get a single instance.
	 *
	 * @return ShutdownScheduler
	 */
	public static function getInstance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register a class call to handle on shutdown.
	 *
	 * @param string $class
	 * @param string $methodName
	 * @param array $methodParams
	 * @param array $constructorParams
	 */
	public function registerClass($class, $methodName, array $methodParams = array(), array $constructorParams = array()) {
		$callback = new Callback();
		$callback->setType(Callback::TYPE_INSTANCE_CALL);
		$callback->setCallableObject($class);
		$callback->setMethodName($methodName);
		$callback->setConstructorArguments($constructorParams);
		$callback->setMethodArguments($methodParams);
		$this->addCallback($callback);
	}

	/**
	 * Register a static class call to handle on shutdown.
	 *
	 * @param string $className
	 * @param string $methodName
	 * @param array $methodParams
	 */
	public function registerStaticClass($className, $methodName, array $methodParams = array()) {
		$callback = new Callback();
		$callback->setType(Callback::TYPE_STATIC_CALL);
		$callback->setMethodName($methodName);
		$callback->setCallableObject($className);
		$callback->setMethodArguments($methodParams);
		$this->addCallback($callback);
	}

	/**
	 * Register a global function to handle on shutdown.
	 *
	 * @param string $methodName
	 * @param array $methodParams
	 */
	public function registerWrapper($methodName, array $methodParams = array()) {
		$callback = new Callback();
		$callback->setType(Callback::TYPE_WRAPPER_CALL);
		$callback->setMethodName($methodName);
		$callback->setMethodArguments($methodParams);
		$this->addCallback($callback);
	}

	/**
	 * Adds a callback unique.
	 *
	 * @param callable|\Insphare\Base\ShutdownScheduler\Callback $callback
	 */
	private function addCallback(Callback $callback) {
		$index = md5(json_encode($callback));
		if (is_object($callback)) {
			$index = spl_object_hash($callback);
		}
		$this->callbacks->offsetSet($index, $callback);
	}

	/**
	 * Processing method on shutdown. Executes all callbacks.
	 */
	public function callRegisteredShutdown() {
		/** @var $callback Callback */
		foreach ($this->callbacks as $callback) {
			switch (true) {
				case $callback->isStaticCall():
					call_user_func_array($callback->getObjectName() . "::" . $callback->getMethodName(), $callback->getMethodArguments());
					break;

				case $callback->isWrapper():
					call_user_func_array($callback->getMethodName(), $callback->getMethodArguments());
					break;

				case $callback->isInstanceCall():
					$reflectionClass = new \ReflectionClass($callback->getObjectName());
					if (null !== $reflectionClass->getConstructor()) {
						$realClass = $reflectionClass->newInstanceArgs($callback->getConstructorArguments());
					}
					else {
						$realClass = $reflectionClass->newInstance();
					}

					$callableData = array(
						$realClass,
						$callback->getMethodName()
					);

					call_user_func_array($callableData, $callback->getMethodArguments());
					break;
			}
		}
	}
}
