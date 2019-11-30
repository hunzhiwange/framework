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

namespace Leevel\Encryption\Safe;

/**
 * 短字符串长度验证
 *
 * @param string $strings
 * @param int    $maxLength
 *
 * @return string
 */
function short_limit(string $strings, int $maxLength = 500): string
{
    $strings = length_limit($strings, $maxLength);
    $strings = str_replace(["\\'", '\\', '#'], '', $strings);
    $strings = custom_htmlspecialchars($strings);

    return preg_replace('/　+/', '', trim($strings));
}

class short_limit
{
}

// import fn.
class_exists(length_limit::class);
class_exists(custom_htmlspecialchars::class);
