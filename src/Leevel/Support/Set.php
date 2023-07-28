<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * Set 是没有重复项的字符串或者整型有序数据结构.
 *
 * - Set 只能包含 string 或 int 值。
 *
 * @see https://docs.hhvm.com/hack/arrays-and-collections/vec-keyset-and-dict
 */
class Set extends Vector
{
    /**
     * 构造函数.
     */
    public function __construct(array $data, string $valueType)
    {
        if (!\in_array($valueType, ['string', 'int'], true)) {
            throw new \InvalidArgumentException(sprintf('KeySet value type must be string or int, %s given.', $valueType));
        }

        parent::__construct($data, $valueType);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet(mixed $index, mixed $newval): void
    {
        parent::offsetSet($index, $newval);
        $this->elements = array_unique($this->elements);
    }
}
