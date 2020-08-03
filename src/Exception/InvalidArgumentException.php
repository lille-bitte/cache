<?php

declare(strict_types=1);

namespace LilleBitte\Cache\Exception;

use InvalidArgumentException as CoreInvalidArgumentException;
use Psr\Cache\InvalidArgumentException as InvalidArgumentExceptionContract;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class InvalidArgumentException extends CoreInvalidArgumentException implements InvalidArgumentExceptionContract
{
}
