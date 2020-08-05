<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Tests\Integration\SimpleCache\Adapter;

use Memcached;
use Cache\IntegrationTests\SimpleCacheTest;
use LilleBitte\Cache\Adapter\MemcachedCachePool;
use LilleBitte\Cache\Bridge\SimpleCache\SimpleCacheBridge;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class MemcachedSimpleCacheIntegrationTest extends SimpleCacheTest
{
	/**
	 * Create cache pool object.
	 *
	 * @return CacheItemPoolInterface
	 */
	private function createCachePool()
	{
		$memcached = new Memcached();
		$memcached->addServer('localhost');
		return new MemcachedCachePool($memcached);
	}

	/**
	 * {@inheritdoc}
	 */
	public function createSimpleCache()
	{
		return new SimpleCacheBridge($this->createCachePool());
	}
}
