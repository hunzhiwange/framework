<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * SetInt 是没有重复项的整型有序数据结构.
 *
 * - Set 只能包含 int 值。
 *
 * @see https://docs.hhvm.com/hack/arrays-and-collections/vec-keyset-and-dict
 */
class SetInt extends Set
{
    /**
     * 构造函数.
     */
    public function __construct(array $data)
    {
        parent::__construct($data, 'int');
    }
}
