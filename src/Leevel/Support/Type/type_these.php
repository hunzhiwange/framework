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

namespace Leevel\Support\Type;

use InvalidArgumentException;

/**
 * 验证参数是否为指定的类型集合.
 *
 * @param mixed $value
 * @param mixed $types
 *
 * @return bool
 */
function type_these($value, $types): bool
{
    if (!type($types, 'string') &&
        !type_array($types, ['string'])) {
        $e = 'The parameter must be string or an array of string elements.';

        throw new InvalidArgumentException($e);
    }

    if (is_string($types)) {
        $types = (array) $types;
    }

    // 类型检查
    foreach ($types as $item) {
        if (type($value, $item)) {
            return true;
        }
    }

    return false;
}

class type_these
{
}

fns(type::class, type_array::class);
