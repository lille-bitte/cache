<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Bridge\SimpleCache\Exception;

use Exception as BaseException;
use Psr\SimpleCache\CacheException as CacheExceptionContract;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class CacheException extends BaseException implements CacheExceptionContract
{
}
