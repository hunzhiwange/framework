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

namespace Leevel\Leevel\Helper;

if (!function_exists('Leevel\\Leevel\\Helper\\value')) {
    include_once __DIR__.'/value.php';
}

/**
 * 取得应用的环境变量.支持 boolean, empty 和 null.
 *
 * @param mixed $name
 * @param mixed $defaults
 *
 * @return mixed
 */
function env(string $name, $defaults = null)
{
    if (false === $value = getenv($name)) {
        $value = value($defaults);
    }

    switch ($value) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return;
    }

    if (is_string($value) && strlen($value) > 1 &&
        '"' === $value[0] && '"' === $value[strlen($value) - 1]) {
        return substr($value, 1, -1);
    }

    return $value;
}
