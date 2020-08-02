<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Adapter;

use Memcache;
use LilleBitte\Cache\CacheItemPool;
use Psr\Cache\CacheItemInterface;

use function serialize;
use function unserialize;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class MemcacheCachePool extends CacheItemPool
{
	/**
	 * @var Memcache
	 */
	private $cache;

	public function __construct(Memcache $cache)
	{
		$this->cache = $cache;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function storeItemToCache(CacheItemInterface $item, $ttl)
	{
		return $this->cache->set($item->getKey(), serialize($item->get()), 0, $ttl);
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
		return $this->cache->delete($key);
	}
}
