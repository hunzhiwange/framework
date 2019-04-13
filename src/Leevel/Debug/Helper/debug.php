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

namespace Leevel\Debug\Helper;

use RuntimeException;

/**
 * Debug 调试.
 *
 * @param array|string $tag
 * @param array        $args
 * @codeCoverageIgnore
 */
function debug($tag, ...$args): void
{
    if (!is_array($tag) && !is_string($tag)) {
        throw new RuntimeException('Tag must be string or array.');
    }

    $normalPrint = false;

    if (is_array($tag)) {
        switch (count($tag)) {
            case 2:
                list($tag, $call) = $tag;

                break;
            case 3:
                list($tag, $call, $normalPrint) = $tag;

                break;
            default:
                throw new RuntimeException('Invalid argument `tag`.');

                break;
        }
    }

    $key = 'LEEVEL_DEBUG_'.$tag;

    if (isset($GLOBALS[$key])) {
        array_unshift($args, $tag);

        if (null !== $call) {
            $call(...$args);
        } else {
            if (true === $normalPrint) {
                var_dump($args);
            } else {
                dump($args);
            }
        }

        unset($GLOBALS[$key]);
    }
}

/**
 * Debug 标记.
 *
 * @param string $tag
 * @codeCoverageIgnore
 */
function debug_tag(string $tag): void
{
    $key = 'LEEVEL_DEBUG_'.$tag;

    $GLOBALS[$key] = true;
}

if (!function_exists('Leevel\\Debug\\Helper\\dump')) {
    include __DIR__.'/dump.php';
}
