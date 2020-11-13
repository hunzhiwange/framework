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

namespace Leevel\Validate\Helper;

use Exception;

/**
 * 验证是否为正常的 JSON 数据.
 *
 * @param mixed $value
 */
function json($value): bool
{
    if (is_object($value) && !method_exists($value, '__toString')) {
        return false;
    }

    if (is_string($value) && class_exists($value) &&
        !method_exists($value, '__toString')) {
        return false;
    }

    try {
        json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR);

        return true;
    } catch (Exception $e) {
        return false;
    }
}

class json
{
}
