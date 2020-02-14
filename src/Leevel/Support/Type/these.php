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

namespace Leevel\Support\Type;

use InvalidArgumentException;

/**
 * 验证参数是否为指定的类型集合.
 *
 * @param mixed $value
 * @param mixed $types
 *
 * @throws \InvalidArgumentException
 */
function these($value, $types): bool
{
    if (!type($types, 'string') && !arr($types, ['string'])) {
        $e = 'The param must be string or an array of string elements.';

        throw new InvalidArgumentException($e);
    }

    if (is_string($types)) {
        $types = [$types];
    }

    foreach ($types as $item) {
        if (type($value, $item)) {
            return true;
        }
    }

    return false;
}

class these
{
}

// import fn.
class_exists(type::class);
class_exists(arr::class);
