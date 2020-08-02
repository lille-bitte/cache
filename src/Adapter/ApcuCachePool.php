<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Adapter;

use LilleBitte\Cache\CacheItemPool;
use Psr\Cache\CacheItemInterface;

use function apcu_clear_cache;
use function apcu_delete;
use function apcu_fetch;
use function apcu_store;
use function serialize;
use function unserialize;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ApcuCachePool extends CacheItemPool
{
	/**
	 * {@inheritdoc}
	 */
	protected function storeItemToCache(CacheItemInterface $item, $ttl)
	{
		apcu_store($item->getKey(), serialize($item->get()), $ttl);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function fetchItemFromCache($key)
	{
		$exists     = false;
		$cachedItem = apcu_fetch($key, $exists);

		if (!$exists) {
			return null;
		}

		return unserialize($cachedItem);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function removeAllItemFromCache()
	{
		return apcu_clear_cache();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function removeItemFromCache($key)
	{
		return apcu_delete($key);
	}
}
