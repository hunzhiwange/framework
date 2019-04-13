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

namespace Leevel\Support\Str;

/**
 * 随机大写字母数字.
 *
 * @param int    $length
 * @param string $charBox
 *
 * @return string
 */
function rand_alpha_num_uppercase(int $length, ?string $charBox = null): string
{
    if (!$length) {
        return '';
    }

    if (null === $charBox) {
        $charBox = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    } else {
        $charBox = strtoupper($charBox);
    }

    return rand_str($length, $charBox);
}

if (!function_exists('Leevel\\Support\\Str\\rand_str')) {
    include __DIR__.'/rand_str.php';
}
