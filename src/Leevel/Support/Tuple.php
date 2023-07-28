<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * Tuple 元组.
 *
 * @see https://docs.hhvm.com/hack/built-in-types/tuples
 */
final class Tuple extends Collection
{
    /**
     * 键类型.
     */
    protected array $keyTypes = ['int'];

    /**
     * 构造函数.
     */
    public function __construct(array $elements = [], string ...$valueTypes)
    {
        $this->valueTypes = $valueTypes;
        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet(mixed $index, mixed $newval): void
    {
        $this->checkType($index, true);
        $this->checkType($newval, typeIndex: $index);
        $this->elements[$index] = $newval;
    }
}
