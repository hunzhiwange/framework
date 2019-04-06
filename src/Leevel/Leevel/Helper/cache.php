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

namespace Leevel\Leevel\Helper;

use Leevel\Cache\ICache;
use Leevel\Leevel\Project;

/**
 * 设置或者获取 cache 值
 *
 * @param null|array|string $key
 * @param mixed             $defaults
 * @param array             $option
 *
 * @return mixed
 */
function cache($key = null, $defaults = null, array $option = [])
{
    $service = Project::singletons()
        ->make(ICache::class);

    if (null === $key) {
        return $service;
    }

    if (is_array($key)) {
        return $service->put($key, null, $option);
    }

    return $service->get($key, $defaults, $option);
}
