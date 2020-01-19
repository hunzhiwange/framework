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
 * 添加模式转义.
 *
 * @param mixed $data
 *
 * @return mixed
 */
function custom_addslashes($data, bool $recursive = true)
{
    if (true === $recursive && is_array($data)) {
        $result = [];
        foreach ($data as $key => $value) {
            $result[custom_addslashes($key)] = custom_addslashes($value);
        }

        return $result;
    }

    if (is_string($data)) {
        $data = addslashes($data);
    }

    return $data;
}

class custom_addslashes
{
}
