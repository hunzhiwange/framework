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
 * html 过滤.
 *
 * @param mixed $data
 * @param int   $maxNum
 *
 * @return mixed
 */
function html_filter($data, int $maxNum = 20000)
{
    if (is_array($data)) {
        $result = [];

        foreach ($data as $key => $val) {
            $result[html_filter($key)] = html_filter($val, $maxNum);
        }

        return $result;
    }

    $data = trim(length_limit((string) ($data), $maxNum));

    $data = preg_replace([
        '/<\s*a[^>]*href\s*=\s*[\'\"]?(javascript|vbscript)[^>]*>/i',
        '/<([^>]*)on(\w)+=[^>]*>/i',
        '/<\s*\/?\s*(script|i?frame)[^>]*\s*>/i',
    ], [
        '<a href="#">',
        '<$1>',
        '&lt;$1&gt;',
    ], $data);

    $data = str_replace('　', '', $data);

    return $data;
}

// @codeCoverageIgnoreStart
if (!function_exists('Leevel\\Encryption\\Safe\\length_limit')) {
    include __DIR__.'/length_limit.php';
}
// @codeCoverageIgnoreEnd
