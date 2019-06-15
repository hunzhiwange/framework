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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Cache\Helper;

use Leevel\Cache\ICache;
use Leevel\Di\Container;

/**
 * cache 服务
 *
 * @return \Leevel\Cache\ICache
 */
function cache(): ICache
{
    return Container::singletons()->make('caches');
}

/**
 * 获取 cache 值
 *
 * @param string     $key
 * @param null|mixed $defaults
 * @param array      $option
 *
 * @return mixed
 */
function cache_get(string $key, $defaults = null, array $option = [])
{
    return cache()->get($key, $defaults, $option);
}

/**
 * 设置 cache 值
 *
 * @param array|string $key
 * @param null|mixed   $value
 * @param array        $option
 */
function cache_set($key, $value = null, array $option = []): void
{
    cache()->put($key, $value, $option);
}

class cache
{
}
