<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Tests\Integration\CachePool\Adapter;

use Cache\IntegrationTests\CachePoolTest;
use LilleBitte\Cache\Adapter\ApcCachePool;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ApcCachePoolIntegrationTest extends CachePoolTest
{
	/**
	 * {@inheritdoc}
	 */
	public function createCachePool()
	{
		return new ApcCachePool();
	}
}
