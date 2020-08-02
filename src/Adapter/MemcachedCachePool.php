<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Adapter;

use Memcached;
use LilleBitte\Cache\CacheItemPool;
use Psr\Cache\CacheItemInterface;

use function serialize;
use function unserialize;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class MemcachedCachePool extends CacheItemPool
{
	/**
	 * @var Memcached
	 */
	private $cache;

	public function __construct(Memcached $cache)
	{
		$this->cache = $cache;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function storeItemToCache(CacheItemInterface $item, $ttl)
	{
		return $this->cache->set($item->getKey(), serialize($item->get()), $ttl);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function fetchItemFromCache($key)
	{
		if (false === ($cachedItem = $this->cache->get($key))) {
			return null;
		}

		return unserialize($cachedItem);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function removeAllItemFromCache()
	{
		return $this->cache->flush();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function removeItemFromCache($key)
	{
		if ($this->cache->delete($key)) {
			return true;
		}

		return $this->cache->getResultCode() === Memcached::RES_NOTFOUND;
	}
}
