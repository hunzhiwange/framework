<?php

declare(strict_types=1);

namespace Leevel\Support\Arr;

use Closure;
use InvalidArgumentException;
use function Leevel\Support\Type\arr;
use Leevel\Support\Type\arr;

/**
 * 数据库 IN 查询条件.
 *
 * @throws \InvalidArgumentException
 */
function in_condition(array $data, int|string $key, ?Closure $filter = null): array
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
