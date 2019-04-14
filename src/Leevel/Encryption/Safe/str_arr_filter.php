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
 * string array 过滤.
 *
 * @param mixed $strings
 *
 * @return mixed
 */
function str_arr_filter($strings)
{
    $result = '';

    if (!is_array($strings)) {
        $strings = explode(',', $strings);
    }

    $strings = array_map(function ($str) {
        return sql_filter($str);
    }, $strings);

    foreach ($strings as $val) {
        if ('' !== $val) {
            $result .= "'".$val."',";
        }
    }

    return preg_replace('/,$/', '', $result);
}

// @codeCoverageIgnoreStart
if (!function_exists('Leevel\\Encryption\\Safe\\sql_filter')) {
    include __DIR__.'/sql_filter.php';
}
// @codeCoverageIgnoreEnd
