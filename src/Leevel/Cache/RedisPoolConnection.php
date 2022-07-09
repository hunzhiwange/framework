<?php

declare(strict_types=1);

namespace Leevel\Cache;

use Leevel\Level\Pool\Connection;
use Leevel\Level\Pool\IConnection;

/**
 * Redis 连接池连接.
 */
class RedisPoolConnection extends Redis implements IConnection
{
    use Connection;
}
