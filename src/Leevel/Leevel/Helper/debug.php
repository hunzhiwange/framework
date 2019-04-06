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

use Closure;

/**
 * Debug 标记.
 *
 * @param string $tag
 * @codeCoverageIgnore
 */
function debug_start(string $tag): void
{
    $key = 'LEEVEL_DEBUG_'.$tag;

    $GLOBALS[$key] = true;
}

/**
 * Debug 进行时.
 *
 * @param string   $tag
 * @param \Closure $call
 * @param array    $args
 * @codeCoverageIgnore
 */
function debug_on(string $tag, Closure $call = null, ...$args): void
{
    $key = 'LEEVEL_DEBUG_'.$tag;

    if (isset($GLOBALS[$key])) {
        if (null !== $call) {
            $call(...$args);
        } else {
            if (!function_exists('Leevel\\Leevel\\Helper\\dump')) {
                include_once __DIR__.'/dump.php';
            }

            if (empty($args)) {
                $args[] = '';
            }

            dump(sprintf('----- `%s` start -----', $tag));
            dump(...$args);
            dump(sprintf('----- `%s` end -----', $tag));
            echo PHP_EOL;
        }
    }
}

/**
 * 清理 Debug 标记.
 *
 * @param string $tag
 * @codeCoverageIgnore
 */
function debug_end(string $tag): void
{
    $key = 'LEEVEL_DEBUG_'.$tag;

    if (isset($GLOBALS[$key])) {
        unset($GLOBALS[$key]);
    }
}
