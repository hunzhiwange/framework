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

namespace Leevel\Support\Helper;

use Leevel\Support\Fn as Fns;

/**
 * 自动导入函数.
 *
 * @param callable|\Closure|string $fn
 * @param array                    $args
 *
 * @return mixed
 */
function fn($fn, ...$args)
{
    static $instance, $loaded = [];

    if (is_string($fn) && in_array($fn, $loaded, true)) {
        return $fn(...$args);
    }

    if (null === $instance) {
        $instance = new Fns();
    }

    $result = $instance->__invoke($fn, ...$args);

    if (is_string($fn)) {
        $loaded[] = $fn;
    }

    return $result;
}
