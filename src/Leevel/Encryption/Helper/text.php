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

namespace Leevel\Encryption\Helper;

/**
 * 字符串文本化.
 */
function text(string $strings, bool $deep = true, array $black = []): string
{
    if (true === $deep && !$black) {
        $black = [
            ' ', '&nbsp;', '&', '=', '-',
            '#', '%', '!', '@', '^', '*', 'amp;',
        ];
    }

    $strings = clean_js($strings);
    $strings = (string) preg_replace('/\s(?=\s)/', '', $strings); // 彻底过滤空格
    $strings = (string) preg_replace('/[\n\r\t]/', ' ', $strings);
    if ($black) {
        $strings = str_replace($black, '', $strings);
    }
    $strings = strip_tags($strings);
    $strings = htmlspecialchars($strings);
    $strings = str_replace("'", '', $strings);

    return $strings;
}

class text
{
}

// import fn.
class_exists(clean_js::class);
