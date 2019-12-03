<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Encryption\Proxy;

use Leevel\Di\Container;
use Leevel\Encryption\Encryption as BaseEncryption;

/**
 * 代理 encryption.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.10
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Encryption
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 加密.
     */
    public static function encrypt(string $value, int $expiry = 0): string
    {
        return self::proxy()->encrypt($value, $expiry);
    }

    /**
     * 解密.
     */
    public static function decrypt(string $value): string
    {
        return self::proxy()->decrypt($value);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseEncryption
    {
        return Container::singletons()->make('encryption');
    }
}
