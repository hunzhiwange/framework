<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * IArray 接口.
 */
interface IArray
{
    /**
     * 对象转数组.
     */
    public function toArray(): array;
}
