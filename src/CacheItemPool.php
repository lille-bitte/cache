<?php

declare(strict_types=1);

namespace LilleBitte\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

use function time;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
abstract class CacheItemPool implements CacheItemPoolInterface
{
	/**
	 * @var array
	 */
	private $deferred = [];

	/**
	 * {@inheritdoc}
	 */
	public function getItem($key)
	{
		$this->validateKey($key);

		if (isset($this->deferred[$key])) {
			return $this->deferred[$key];
		}

		return new CacheItem($key, null);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getItems(array $keys = array())
	{
		$items = [];

		foreach ($keys as $key) {
			$items[] = $this->getItem($key);
		}

		return $items;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasItem($key)
	{
		return $this->getItem($key)->isHit();
	}

	/**
	 * {@inheritdoc}
	 */
	public function clear()
	{
		$this->deferred = [];
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function deleteItem($key)
	{
		$this->validateKey($key);

		// remove from deferred item list.
		unset($this->deferred[$key]);

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function deleteItems(array $keys)
	{
		foreach ($keys as $key) {
			if (false === $this->deleteItem($key)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(CacheItemInterface $item)
	{
		if (null !== ($expirationTimestamp = $item->getExpirationTimestamp())) {
			$timeToLive = $expirationTimestamp - time();

			if ($timeToLive < 0) {
				return $this->deleteItem($item->getKey());
			}
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function saveDeferred(CacheItemInterface $item)
	{
		$this->deferred[$item->getKey()] = $item;
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function commit()
	{
		$isSaved = true;

		foreach ($this->deferred as $delayedItem) {
			if (!$this->save($delayedItem)) {
				$isSaved = false;
			}
		}

		$this->deferred = [];
		return $isSaved;
	}

	/**
	 * Validate given cache key.
	 *
	 * @param string $key Cache key.
	 * @return boolean
	 * @throws \Psr\Cache\InvalidArgumentException If cache key is invalid.
	 */
	private function validateKey($key)
	{
		if (preg_match('/[\{\}\(\)\/\\\@\:]+/', $key)) {
			throw new InvalidArgumentException(
				"Invalid cache key provided."
			);
		}

		return;
	}
}
