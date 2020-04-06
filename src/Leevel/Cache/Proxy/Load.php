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

namespace Leevel\Cache\Proxy;

use Leevel\Cache\Load as BaseLoad;
use Leevel\Di\Container;

/**
 * 代理 cache.load.
 *
 * @codeCoverageIgnore
 */
class Load
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
     * 载入缓存数据.
     *
     * - 系统自动存储缓存到内存，可重复执行不会重复载入数据.
     */
    public static function data(array $names, ?int $expire = null, bool $force = false): array
    {
        return self::proxy()->data($names, $expire, $force);
    }

    /**
     * 刷新缓存数据.
     */
    public static function refresh(array $names): void
    {
        self::proxy()->refresh($names);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseLoad
    {
        return Container::singletons()->make('cache.load');
    }
}
