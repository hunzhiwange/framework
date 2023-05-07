<?php

declare(strict_types=1);

namespace Leevel\Encryption\Proxy;

use Leevel\Di\Container;
use Leevel\Encryption\Encryption as BaseEncryption;

/**
 * 代理 encryption.
 *
 * @method static string       encrypt(string $value, int $expiry = 0) 加密.
 * @method static string|false decrypt(string $value)                  解密.
 */
class Encryption
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseEncryption
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('encryption');
    }
}
