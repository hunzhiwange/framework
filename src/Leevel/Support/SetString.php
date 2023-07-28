<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * SetString 是没有重复项的字符串有序数据结构.
 *
 * - Set 只能包含 string 值。
 *
 * @see https://docs.hhvm.com/hack/arrays-and-collections/vec-keyset-and-dict
 */
class SetString extends Set
{
    /**
     * 构造函数.
     */
    public function __construct(array $data)
    {
        parent::__construct($data, 'string');
    }
}
