<?php

declare(strict_types=1);

namespace LilleBitte\Cache;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;

use function time;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class CacheItem implements CacheItemInterface
{
	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var mixed
	 */
	private $value;

	/**
	 * @var int|null
	 */
	private $expirationTimestamp;

	public function __construct($key, $value = null)
	{
		$this->key = $key;
		$this->set($value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get()
	{
		if (!$this->isHit()) {
			return null;
		}

		return $this->value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isHit()
	{
		if (is_null($this->value)) {
			return false;
		}

		if ($this->getExpirationTimestamp() !== null) {
			return $this->getExpirationTimestamp() > time();
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set($value)
	{
		$this->value = $value;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function expiresAt($expiration)
	{
		if ($expiration instanceof DateTimeInterface) {
			$this->expirationTimestamp = $expiration->getTimestamp();
		} else if (is_int($expiration) || is_null($expiration)) {
			$this->expirationTimestamp = $expiration;
		}

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function expiresAfter($time)
	{
		if ($time instanceof DateInterval) {
			$date = new DateTime();
			$date->add($time);
			$this->expirationTimestamp = $date->getTimestamp();
		} else if (is_int($time)) {
			$this->expirationTimestamp = time() + $time;
		} else if (is_null($time)) {
			$this->expirationTimestamp = null;
		}

		return $this;
	}

	/**
	 * Get expiration timestamp.
	 *
	 * @return int
	 */
	public function getExpirationTimestamp()
	{
		return $this->expirationTimestamp;
	}
}
