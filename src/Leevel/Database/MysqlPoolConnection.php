<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Level\Pool\Connection;
use Leevel\Level\Pool\IConnection;

/**
 * MySQL 连接池连接.
 */
class MysqlPoolConnection extends Mysql implements IConnection
{
    use Connection;
}
