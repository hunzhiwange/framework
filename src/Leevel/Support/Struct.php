<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * Struct 结构体.
 *
 * - 结构体具有顺序性。
 *
 * @see https://docs.hhvm.com/hack/built-in-types/shape
 */
final class Struct extends Collection
{
    /**
     * 键类型.
     */
    protected array $keyTypes = ['string'];

    protected bool $shouldValidateElement = false;

    /**
     * 构造函数.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $elements, string ...$valueTypes)
    {
        if (0 === \count($valueTypes)) {
            throw new \InvalidArgumentException('The value type cannot be empty.');
        }

        if (\count($valueTypes) !== \count($elements)) {
            throw new \InvalidArgumentException('The number of elements does not match the number of types.');
        }

        $newValueTypes = [];
        $index = 0;
        foreach ($elements as $k => $v) {
            $newValueTypes[$k] = $valueTypes[$index];
            ++$index;
        }

        $this->valueTypes = $newValueTypes;
        parent::__construct($elements);
        $this->shouldValidateElement = true;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function offsetSet(mixed $index, mixed $newval): void
    {
        $this->checkType($index, true);
        $this->checkType($newval, typeIndex: $index);
        $this->elements[$index] = $newval;

        if ($this->shouldValidateElement && \count($this->valueTypes) !== \count($this->elements)) {
            throw new \InvalidArgumentException('The number of elements does not match the number of types.');
        }
    }
}
