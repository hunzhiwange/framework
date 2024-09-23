<?php

declare(strict_types=1);

namespace Tests\Cache\Fixtures;

use Leevel\Cache\ICache;
use Leevel\Cache\Manager as Managers;

class Manager1 extends Managers
{
    private static ICache $connect;

    public function connect(?string $connect = null, bool $newConnect = false, mixed ...$arguments): ICache
    {
        return static::$connect;
    }

    public static function setConnect(ICache $connect): void
    {
        static::$connect = $connect;
    }
}
