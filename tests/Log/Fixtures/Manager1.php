<?php

declare(strict_types=1);

namespace Tests\Log\Fixtures;

use Leevel\Log\ILog;
use Leevel\Log\Manager as Managers;

class Manager1 extends Managers
{
    private static ILog $connect;

    public function connect($options = null, bool $newConnect = false): ILog
    {
        return static::$connect;
    }

    public static function setConnect(ILog $connect): void
    {
        static::$connect = $connect;
    }
}
