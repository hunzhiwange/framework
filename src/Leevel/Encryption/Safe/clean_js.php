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

namespace Leevel\Encryption\Safe;

/**
 * 过滤掉 javascript.
 *
 * @param string $strings 待过滤的字符串
 *
 * @return string
 */
function clean_js(string $strings): string
{
    $strings = trim($strings);

    $strings = stripslashes($strings);
    $strings = preg_replace('/<!--?.*-->/', '', $strings); // 完全过滤注释
    $strings = preg_replace('/<\?|\?>/', '', $strings); // 完全过滤动态代码
    $strings = preg_replace('/<script?.*\/script>/', '', $strings); // 完全过滤 js

    $strings = preg_replace('/<\/?(html|head|meta|link|base|body|title|style|script|form|iframe|frame|frameset)[^><]*>/i', '', $strings); // 过滤多余 html

    while (preg_match('/(<[^><]+)(lang|onfinish|onmouse|onexit|onerror|onclick|onkey|onload|onchange|onfocus|onblur)[^><]+/i', $strings, $matches)) { // 过滤 on 事件
        $strings = str_replace($matches[0], $matches[1], $strings);
    }

    while (preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $strings, $matches)) {
        $strings = str_replace($matches[0], $matches[1].$matches[3], $strings);
    }

    return $strings;
}

class clean_js
{
}
