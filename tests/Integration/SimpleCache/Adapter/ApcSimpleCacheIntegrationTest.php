<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Tests\Integration\SimpleCache\Adapter;

use Cache\IntegrationTests\SimpleCacheTest;
use LilleBitte\Cache\Adapter\ApcCachePool;
use LilleBitte\Cache\Bridge\SimpleCache\SimpleCacheBridge;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ApcSimpleCacheIntegrationTest extends SimpleCacheTest
{
	/**
	 * Create cache pool object.
	 *
	 * @return CacheItemPoolInterface
	 */
	private function createCachePool()
	{
		return new ApcCachePool();
	}

	/**
	 * {@inheritdoc}
	 */
	public function createSimpleCache()
	{
		return new SimpleCacheBridge($this->createCachePool());
	}
}
