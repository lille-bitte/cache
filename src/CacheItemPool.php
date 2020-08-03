<?php

declare(strict_types=1);

namespace LilleBitte\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use LilleBitte\Cache\Exception\InvalidArgumentException;

use function time;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
abstract class CacheItemPool implements CacheAwareAdapterInterface
{
	/**
	 * @var array
	 */
	private $deferred = [];

	/**
	 * Deferred cache items must be committed before
	 * call __destruct().
	 */
	public function __destruct()
	{
		$this->commit();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getItem($key)
	{
		$this->validateKey($key);

		if (isset($this->deferred[$key])) {
			$immutableCacheItem = clone $this->deferred[$key];
			return $immutableCacheItem;
		}

		return new CacheItem($key, $this->fetchItemFromCache($key));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getItems(array $keys = array())
	{
		$items = [];

		foreach ($keys as $key) {
			$items[$key] = $this->getItem($key);
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
		return $this->removeAllItemFromCache();
	}

	/**
	 * {@inheritdoc}
	 */
	public function deleteItem($key)
	{
		$this->validateKey($key);

		// remove from deferred item list.
		unset($this->deferred[$key]);

		return $this->removeItemFromCache($key);
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
		$timeToLive = null;

		if (null !== ($expirationTimestamp = $item->getExpirationTimestamp())) {
			$timeToLive = $expirationTimestamp - time();

			if ($timeToLive < 0) {
				return $this->deleteItem($item->getKey());
			}
		}

		return $this->storeItemToCache($item, $timeToLive);
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
		if (!is_string($key)) {
			throw new InvalidArgumentException(
				"Cache key must be a string."
			);
		}

		if (empty($key)) {
			throw new InvalidArgumentException(
				"A cache key must not be an empty string."
			);
		}

		if (preg_match('/[\{\}\(\)\/\\\@\:]+/', $key)) {
			throw new InvalidArgumentException(
				"Invalid cache key provided."
			);
		}

		return;
	}

	/**
	 * Store cache item to cache storage.
	 *
	 * @param CacheItemInterface $item Cache item object.
	 * @param int|null $ttl Expiration seconds from now.
	 * @return true If saved.
	 */
	abstract protected function storeItemToCache(CacheItemInterface $item, $ttl);

	/**
	 * Get item from cache.
	 *
	 * @param string $key Key of cached item.
	 * @return mixed
	 */
	abstract protected function fetchItemFromCache($key);

	/**
	 * Remove all cached item from cache.
	 *
	 * @return boolean
	 */
	abstract protected function removeAllItemFromCache();

	/**
	 * Remove cached item from cache.
	 *
	 * @param string $key
	 * @return boolean
	 */
	abstract protected function removeItemFromCache($key);
}
