<?php

declare(strict_types=1);

namespace Tests\Session\Fixtures;

use Leevel\Session\ISession;
use Leevel\Session\Manager as Managers;

class Manager1 extends Managers
{
    private static ISession $connect;

    public function connect($configs = null, bool $newConnect = false): ISession
    {
        return static::$connect;
    }

    public static function setConnect(ISession $connect): void
    {
        static::$connect = $connect;
    }
}
