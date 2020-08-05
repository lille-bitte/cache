<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Bridge\SimpleCache;

use Traversable;
use LilleBitte\Cache\Bridge\SimpleCache\Exception\InvalidArgumentException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException as CacheInvalidArgumentException;
use Psr\SimpleCache\CacheInterface;

use function iterator_to_array;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class SimpleCacheBridge implements CacheInterface
{
	/**
	 * @var CacheItemPoolInterface
	 */
	private $cache;

	public function __construct(CacheItemPoolInterface $cache)
	{
		$this->cache = $cache;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get($key, $default = null)
	{
		try {
			$cachedItem = $this->cache->getItem($key);
		} catch (CacheInvalidArgumentException $e) {
			throw new InvalidArgumentException($e->getMessage(), $e->getCode());
		}

		if (!$cachedItem->isHit()) {
			return $default;
		}

		return $cachedItem->get();
	}

	/**
	 * {@inheritdoc}
	 */
	public function set($key, $value, $ttl = null)
	{
		try {
			$cachedItem = $this->cache->getItem($key);
			$cachedItem->expiresAfter($ttl);
		} catch (CacheInvalidArgumentException $e) {
			throw new InvalidArgumentException($e->getMessage(), $e->getCode());
		}

		$cachedItem->set($value);

		return $this->cache->save($cachedItem);
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete($key)
	{
		try {
			return $this->cache->deleteItem($key);
		} catch (CacheInvalidArgumentException $e) {
			throw new InvalidArgumentException($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function clear()
	{
		return $this->cache->clear();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMultiple($keys, $default = null)
	{
		if (!is_array($keys) && !($keys instanceof Traversable)) {
			throw new InvalidArgumentException(
				"Keys must be an array or instance of 'Traversable'"
			);
		}

		$keys = ($keys instanceof Traversable)
			? iterator_to_array($keys, false)
			: $keys;

		try {
			$cachedItems = $this->cache->getItems($keys);
		} catch (CacheInvalidArgumentException $e) {
			throw new InvalidArgumentException($e->getMessage(), $e->getCode());
		}

		return $this->neutralizeCachedItems($cachedItems, $default);
	}

	/**
	 * Neutralize cached items when one of them were considered stale.
	 *
	 * @param array $cachedItems Cached items.
	 * @param mixed $default Default value when one of the cached item were
	 *                       considered stale.
	 * @return \Generator
	 */
	private function neutralizeCachedItems($cachedItems, $default = null)
	{
		foreach ($cachedItems as $key => $cachedItem) {
			if (!$cachedItem->isHit()) {
				yield $key => $default;
			} else {
				yield $key => $cachedItem->get();
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function setMultiple($values, $ttl = null)
	{
		if (!is_array($values) && !($values instanceof Traversable)) {
			throw new InvalidArgumentException(
				"Values must be an array or instance of 'Traversable'"
			);
		}

		$gotException = null;
		$isSaved      = true;

		foreach ($values as $key => $value) {
			$key = is_int($key) ? (string)$key : $key;

			$this->validateCacheKey($key);

			try {
				$cachedItem = $this->cache->getItem($key);
			} catch (CacheInvalidArgumentException $e) {
				$gotException = new InvalidArgumentException($e->getMessage(), $e->getCode());
				break;
			}

			$cachedItem->set($value);

			try {
				$cachedItem->expiresAfter($ttl);
			} catch (CacheInvalidArgumentException $e) {
				$gotException = new InvalidArgumentException($e->getMessage(), $e->getCode());
				break;
			}

			$isSaved = $isSaved && $this->cache->saveDeferred($cachedItem);
		}

		if (null !== $gotException) {
			throw $gotException;
		}

		return $isSaved && $this->cache->commit();
	}

	/**
	 * Validate cache key.
	 *
	 * @param string $key Cache key being validated.
	 * @return void
	 * @throws InvalidArgumentException If cache key is invalid.
	 */
	private function validateCacheKey($key)
	{
		if (!is_string($key)) {
			throw new InvalidArgumentException("Cache key must be a string.");
		}

		if (preg_match('/[\{\}\(\)\/\\\@\:]+/', $key)) {
			throw new InvalidArgumentException("Invalid cache key.");
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function deleteMultiple($keys)
	{
		if (!is_array($keys) && !($keys instanceof Traversable)) {
			throw new InvalidArgumentException(
				"Cache keys must be an array or instance of 'Traversable'."
			);
		}

		$keys = ($keys instanceof Traversable)
			? iterator_to_array($keys, false)
			: $keys;

		try {
			return $this->cache->deleteItems($keys);
		} catch (CacheInvalidArgumentException $e) {
			throw new InvalidArgumentException($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function has($key)
	{
		try {
			return $this->cache->hasItem($key);
		} catch (CacheInvalidArgumentException $e) {
			throw new InvalidArgumentException($e->getMessage(), $e->getCode());
		}
	}
}
