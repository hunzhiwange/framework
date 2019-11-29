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

use Leevel\Di\Container;

/**
 * 设置 cache 值
 *
 * @param array|string $key
 * @param null|mixed   $value
 * @param array        $option
 */
function cache_set($key, $value = null, array $option = []): void
{
    /** @var \Leevel\Cache\ICache $cache */
    $cache = Container::singletons()->make('caches');
    $cache->put($key, $value, $option);
}

class cache_set
{
}
