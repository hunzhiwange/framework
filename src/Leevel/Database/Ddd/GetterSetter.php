<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

/**
 * 实体 Getter Setter.
 */
trait GetterSetter
{
    /**
     * Database connect.
     */
    private static ?string $databaseConnect = null;

    /**
     * Set database connect.
     */
    public static function withConnect(?string $connect = null): void
    {
        static::$databaseConnect = $connect;
    }

    /**
     * Get database connect.
     */
    public static function connect(): ?string
    {
        if (!(static::definedEntityConstant('WITHOUT_GLOBAL_CONNECT')
                && true === static::entityConstant('WITHOUT_GLOBAL_CONNECT'))
            && isset(static::$globalConnect)) {
            return static::$globalConnect;
        }

        return static::$databaseConnect ??
            (\defined($constConnect = static::class.'::CONNECT') ?
                \constant($constConnect) : null);
    }
}
