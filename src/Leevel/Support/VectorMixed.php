<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * VectorMixed 动态数组是有序的、可迭代的混合型数据结构.
 *
 * @see https://docs.hhvm.com/hack/arrays-and-collections/vec-keyset-and-dict
 */
final class VectorMixed extends Vector
{
    /**
     * 构造函数.
     */
    public function __construct(array $data)
    {
        parent::__construct($data, '');
    }
}
