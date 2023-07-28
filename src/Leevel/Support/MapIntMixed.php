<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * MapIntMixed 映射是键值为 int 的混合型有序键值数据结构.
 *
 * - 键必须是 int。
 * - 根据插入顺序排序。
 *
 * @see https://docs.hhvm.com/hack/arrays-and-collections/vec-keyset-and-dict
 */
class MapIntMixed extends Map
{
    /**
     * 构造函数.
     */
    public function __construct(array $data)
    {
        parent::__construct($data, 'int', '');
    }
}
