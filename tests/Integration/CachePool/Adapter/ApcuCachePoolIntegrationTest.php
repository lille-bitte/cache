<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Tests\Integration\CachePool\Adapter;

use Cache\IntegrationTests\CachePoolTest;
use LilleBitte\Cache\Adapter\ApcuCachePool;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ApcuCachePoolIntegrationTest extends CachePoolTest
{
	/**
	 * {@inheritdoc}
	 */
	public function createCachePool()
	{
		return new ApcuCachePool();
	}
}
