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

namespace Leevel\Support\Arr;

use Closure;
use InvalidArgumentException;
use function Leevel\Support\Type\arr;
use Leevel\Support\Type\arr;

/**
 * 数据库 IN 查询条件.
 *
 * @param int|string $key
 *
 * @throws \InvalidArgumentException
 */
function in_condition(array $data, $key, ?Closure $filter = null): array
{
    if (!arr($data, ['array'])) {
        throw new InvalidArgumentException('Data item must be array.');
    }

    $data = array_unique(array_column($data, $key));
    if (null === $filter) {
        return $data;
    }

    return array_map(fn ($v) => $filter($v), $data);
}

class in_condition
{
}

// import fn.
class_exists(arr::class);
