<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * MapString 映射是键值为 string 的有序键值数据结构.
 *
 * - 键必须是 string。
 * - 根据插入顺序排序。
 *
 * @see https://docs.hhvm.com/hack/arrays-and-collections/vec-keyset-and-dict
 */
class MapString extends Map
{
    /**
     * 构造函数.
     */
    public function __construct(array $data, string $valueType)
    {
        parent::__construct($data, 'string', $valueType);
    }
}
