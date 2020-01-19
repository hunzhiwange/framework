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
 * HTML 过滤.
 *
 * @param mixed $data
 *
 * @return mixed
 */
function html_filter($data)
{
    if (is_array($data)) {
        $result = [];
        foreach ($data as $key => $val) {
            $result[html_filter($key)] = html_filter($val);
        }

        return $result;
    }

    $data = trim((string) $data);
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

class html_filter
{
}
