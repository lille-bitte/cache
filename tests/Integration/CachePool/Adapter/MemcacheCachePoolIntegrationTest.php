<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Tests\Integration\CachePool\Adapter;

use Memcache;
use Cache\IntegrationTests\CachePoolTest;
use LilleBitte\Cache\Adapter\MemcacheCachePool;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class MemcacheCachePoolIntegrationTest extends CachePoolTest
{
    /**
     * {@inheritdoc}
     */
    public function createCachePool()
    {
        $memcache = new Memcache();
        $memcache->addServer('localhost', 11211);
        return new MemcacheCachePool($memcache);
    }
}
