<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * Struct 结构体.
 *
 * @see https://docs.hhvm.com/hack/built-in-types/shape
 */
final class Struct extends Collection
{
    /**
     * 键类型.
     */
    protected array $keyTypes = ['string'];

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
