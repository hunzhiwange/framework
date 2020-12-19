<?php

declare(strict_types=1);

namespace Leevel\Cache;

use Leevel\Protocol\Pool\Connection;
use Leevel\Protocol\Pool\IConnection;

/**
 * Redis 连接池连接.
 */
class RedisPoolConnection extends Redis implements IConnection
{
    use Connection;
}
