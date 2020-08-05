<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Tests\Integration\SimpleCache\Adapter;

use Memcache;
use Cache\IntegrationTests\SimpleCacheTest;
use LilleBitte\Cache\Adapter\MemcacheCachePool;
use LilleBitte\Cache\Bridge\SimpleCache\SimpleCacheBridge;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class MemcacheSimpleCacheIntegrationTest extends SimpleCacheTest
{
    /**
     * Create cache pool object.
     *
     * @return CacheItemPoolInterface
     */
    private function createCachePool()
    {
        $memcache = new Memcache();
        $memcache->addServer('localhost');
        return new MemcacheCachePool($memcache);
    }

    /**
     * {@inheritdoc}
     */
    public function createSimpleCache()
    {
        return new SimpleCacheBridge($this->createCachePool());
    }
}
