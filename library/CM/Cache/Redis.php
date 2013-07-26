<?php

/**
 * Uses 'phpredis' extension: https://github.com/nicolasff/phpredis
 */

class CM_Cache_Redis extends CM_Cache_Abstract {

	protected static $_instance;

	/** @var Redis */
	private $_redis = null;

	/** @var Closure|null */
	private $_subscribeCallback;

	public function __construct() {
		$this->_redis = new Redis();
		$server = self::_getConfig()->server;
		try {
			$this->_redis->connect($server['host'], $server['port']);
		} catch (RedisException $e) {
			throw new CM_Exception('Cannot connect to redis server `' . $server['host'] . '` on port `' . $server['port'] . '`: ' . $e->getMessage());
		}
	}

	protected function _getName() {
		return 'Redis';
	}

	protected function _set($key, $data, $lifeTime = null) {
		throw new CM_Exception_NotImplemented();
	}

	protected function _get($key) {
		throw new CM_Exception_NotImplemented();
	}

	protected function _delete($key) {
		throw new CM_Exception_NotImplemented();
	}

	/**
	 * Add a value to a set
	 *
	 * @param string $key
	 * @param string $value
	 */
	protected function _sAdd($key, $value) {
		$this->_redis->sAdd($key, $value);
	}

	/**
	 * Remove a value from a set
	 *
	 * @param string $key
	 * @param string $value
	 */
	protected function _sRem($key, $value) {
		$this->_redis->sRem($key, $value);
	}

	/**
	 * Remove and return all members of a set
	 *
	 * @param string $key
	 * @return string[]
	 */
	protected function _sFlush($key) {
		$values = $this->_redis->multi()->sMembers($key)->delete($key)->exec();
		return $values[0];
	}

	/**
	 * Add a value to a list
	 *
	 * @param string $key
	 * @param string $value
	 */
	protected function _lPush($key, $value) {
		$this->_redis->lPush($key, $value);
	}

	/**
	 * Remove and return a value from a list
	 *
	 * @param string $key
	 * @return string
	 */
	protected function _rPop($key) {
		return $this->_redis->rPop($key);
	}

	/**
	 * @param string $key
	 * @param int    $score
	 * @param string $value
	 * @return mixed
	 */
	protected function _zAdd($key, $score, $value) {
		return $this->_redis->zAdd($key, $score, $value);
	}

	protected function _zRem($key, $value) {
		return $this->_redis->zRem($key, $value);

	}

	/**
	 * @param              $key
	 * @param string       $start
	 * @param string       $end
	 * @param int|null     $count
	 * @param int|null     $offset
	 * @param boolean|null $returnScore
	 * @return array
	 */
	protected function _zRangeByScore($key, $start, $end, $count = null, $offset = null, $returnScore = null) {
		$options = array();
		if ($count || $offset) {
			$count = (null !== $count) ? (int) $count : -1;
			$offset = (null !== $offset) ? (int) $count : 0;
			$options['limit'] = array($offset, $count);
		}
		if ($returnScore) {
			$options['whithscores'] = true;
		}
		return $this->_redis->zRangeByScore($key, $start, $end, $options);
	}

	/**
	 * @param string $channel
	 * @param string $msg
	 */
	protected function _publish($channel, $msg) {
		$this->_redis->publish($channel, $msg);
	}

	/**
	 * @param string|string[] $channels
	 * @param Closure         $callback
	 */
	public function subscribe($channels, Closure $callback) {
		$channels = (array) $channels;
		$this->_subscribeCallback = $callback;
		$this->_redis->setOption(Redis::OPT_READ_TIMEOUT, 86400 * 100);
		$this->_redis->subscribe($channels, array($this, '_subscribeCallback'));
	}

	/**
	 * @param Redis  $redis
	 * @param string $channel
	 * @param string $message
	 */
	public function _subscribeCallback($redis, $channel, $message) {
		try {
			$callback = $this->_subscribeCallback;
			$callback($channel, $message);
		} catch (Exception $e) {
			CM_Bootloader::getInstance()->handleException($e);
		}
	}

	protected function _flush() {
		$this->_redis->flushAll();
	}
}
