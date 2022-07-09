<?php

declare(strict_types=1);

namespace Leevel\Support\Arr;

use Closure;
use InvalidArgumentException;
use Leevel\Support\Type\Arr;

class InCondition
{
    /**
     * 数据库 IN 查询条件.
     * @throws \InvalidArgumentException
     */
    public static function handle(array $data, int|string $key, ?Closure $filter = null): array
    {
        if (!Arr::handle($data, ['array'])) {
            throw new InvalidArgumentException('Data item must be array.');
        }

        $data = array_unique(array_column($data, $key));
        if (null === $filter) {
            return $data;
        }

        return array_map(fn ($v) => $filter($v), $data);
    }
}
