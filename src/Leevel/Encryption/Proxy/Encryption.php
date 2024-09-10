<?php

declare(strict_types=1);

namespace Leevel\Encryption\Proxy;

use Leevel\Di\Container;
use Leevel\Encryption\Encryption as BaseEncryption;

/**
 * 代理 encryption.
 */
class Encryption
{
    /**
     * 代理服务.
     */
    public static function proxy(): BaseEncryption
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('encryption');
    }
}
