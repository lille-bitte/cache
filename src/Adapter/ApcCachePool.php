<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Adapter;

use LilleBitte\Cache\CacheItemPool;
use Psr\Cache\CacheItemInterface;

use function apc_clear_cache;
use function apc_delete;
use function apc_fetch;
use function apc_store;
use function serialize;
use function unserialize;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ApcCachePool extends CacheItemPool
{
	/**
	 * {@inheritdoc}
	 */
	protected function storeItemToCache(CacheItemInterface $item, $ttl)
	{
		return apc_store(
			$item->getKey(),
			serialize([$item->get(), $item->getExpirationTimestamp()]),
			$ttl ?? 0
		);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function fetchItemFromCache($key)
	{
		$exists     = false;
		$cachedItem = apc_fetch($key, $exists);

		if (!$exists) {
			return [$exists, null, null];
		}

		$data = unserialize($cachedItem);
		return [$exists, $data[0], $data[1]];
	}

	/**
	 * {@inheritdoc}
	 */
	protected function removeAllItemFromCache()
	{
		return apc_clear_cache('user');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function removeItemFromCache($key)
	{
		apc_delete($key);
		return true;
	}
}
