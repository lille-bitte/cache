<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Exception;

use Exception as CoreException;
use Psr\Cache\CacheException as CacheExceptionContract;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class CacheException extends CoreException implements CacheExceptionContract
{
}
