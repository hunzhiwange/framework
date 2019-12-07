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

namespace Leevel\Cache\Helper;

use Leevel\Di\Container;

/**
 * 获取 cache 值.
 *
 * @param null|mixed $defaults
 *
 * @return mixed
 */
function get(string $key, $defaults = null, array $option = [])
{
    /** @var \Leevel\Cache\Manager $cache */
    $cache = Container::singletons()->make('caches');

    return $cache->get($key, $defaults, $option);
}

class get
{
}
