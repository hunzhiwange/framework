<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

/**
 * 实体数据库连接.
 */
trait Connect
{
    /**
     * Database connect.
     */
    private static ?string $connect = null;

    /**
     * Set database connect.
     */
    public static function withConnect(?string $connect = null): void
    {
        static::$connect = $connect;
    }

    /**
     * Get database connect.
     */
    public static function connect(): ?string
    {
        return static::$connect ??
            (defined($constConnect = static::class.'::CONNECT') ?
                constant($constConnect) :  null);
    }
}
