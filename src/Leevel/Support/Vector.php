<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * Vector 动态数组是有序的、可迭代的数据结构.
 *
 * @see https://docs.hhvm.com/hack/arrays-and-collections/vec-keyset-and-dict
 */
class Vector extends Collection
{
    /**
     * 键类型.
     */
    protected array $keyTypes = ['int'];

    /**
     * 构造函数.
     */
    public function __construct(array $data, string $valueType)
    {
        parent::__construct($data, $valueType ? [$valueType] : []);
    }
}
