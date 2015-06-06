<?php
namespace Insphare\Base;

class Client {

	/**
	 * Contains the global-variable $_SERVER values.
	 *
	 * @var array
	 */
	private $server = array();

	/**
	 * @param array $server
	 */
	public function __construct(array $server) {
		$this->server = (array)$server;
	}

	/**
	 * @return string
	 */
	public function getIP() {
		$ip = '0.0.0.0';
		if (isset($this->server['REMOTE_ADDR'])) {
			$ip = $this->server['REMOTE_ADDR'];
		}

		return $ip;
	}

	/**
	 * @return string
	 */
	public function getHost() {
		$host = '0.0.0.0.0.0';
		if (isset($this->server['REMOTE_ADDR'])) {
			$host = gethostbyaddr($this->server['REMOTE_ADDR']);
		}

		return $host;
	}

	/**
	 * @return string
	 */
	public function getUserAgent() {
		$agent = 'not available, because maybe i\'m cli!';
		if (isset($this->server['HTTP_USER_AGENT'])) {
			$agent = $this->server['HTTP_USER_AGENT'];
		}

		return $agent;
	}

	/**
	 * @return string
	 */
	public function getComputerHash() {
		return sha1($this->getIP() . $this->getHost() . $this->getUserAgent());
	}

	/**
	 * @return string
	 */
	public function getHostHash() {
		return sha1($this->getIP() . $this->getHost());
	}
}
