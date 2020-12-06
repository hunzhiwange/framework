<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 过滤 JavaScript.
 */
function clean_js(string $strings): string
{
    $strings = trim($strings);
    $strings = stripslashes($strings);
    $strings = (string) preg_replace('/<!--?.*-->/', '', $strings); // 完全过滤注释
    $strings = (string) preg_replace('/<\?|\?>/', '', $strings); // 完全过滤动态代码
    $strings = (string) preg_replace('/<script?.*\/script>/', '', $strings); // 完全过滤 js
    $strings = (string) preg_replace('/<\/?(html|head|meta|link|base|body|title|style|script|form|iframe|frame|frameset)[^><]*>/i', '', $strings); // 过滤多余 html

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
