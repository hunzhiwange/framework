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
 * 字符过滤.
 *
 * @param mixed $data
 * @param int   $maxNum
 *
 * @return mixed
 */
function str_filter($data, int $maxNum = 20000)
{
    if (is_array($data)) {
        $result = [];

        foreach ($data as $key => $val) {
            $result[str_filter($key)] = str_filter($val, $maxNum);
        }

        return $result;
    }

    $data = trim(length_limit((string) ($data), $maxNum));

    $data = preg_replace(
            '/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/',
            '&\\1',
            custom_htmlspecialchars($data)
        );

    $data = str_replace('　', '', $data);

    return $data;
}

class str_filter
{
}

// import fn.
class_exists(length_limit::class);
class_exists(custom_htmlspecialchars::class);
