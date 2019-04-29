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
 * 字符 HTML 安全实体.
 *
 * @param mixed $data
 *
 * @return mixed
 */
function custom_htmlspecialchars($data)
{
    if (!is_array($data)) {
        $data = (array) $data;
    }

    $data = array_map(function ($data) {
        if (is_string($data)) {
            $data = htmlspecialchars(trim($data));
        }

        return $data;
    }, $data);

    if (1 === count($data)) {
        $data = reset($data);
    }

    return $data;
}

class custom_htmlspecialchars
{
}
