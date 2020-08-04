<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Tests\Integration\CachePool\Adapter;

use Memcached;
use Cache\IntegrationTests\CachePoolTest;
use LilleBitte\Cache\Adapter\MemcachedCachePool;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class MemcachedCachePoolIntegrationTest extends CachePoolTest
{
	/**
	 * {@inheritdoc}
	 */
	public function createCachePool()
	{
		$memcached = new Memcached();
		$memcached->addServer('localhost', 11211);
		return new MemcachedCachePool($memcached);
	}
}
