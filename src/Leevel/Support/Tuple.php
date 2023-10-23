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

        $this->valueTypes = $valueTypes;
        parent::__construct($elements);
        $this->shouldValidateElement = true;
        $this->checkMatch();
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet(mixed $index, mixed $newval): void
    {
        $this->checkType($index, true);
        $this->checkType($newval, typeIndex: $index);
        $this->elements[$index] = $newval;
        $this->checkMatch();
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function checkMatch(): void
    {
        if (!$this->shouldValidateElement) {
            return;
        }

        if (\count($this->valueTypes) !== \count($this->elements)) {
            throw new \InvalidArgumentException('The number of elements does not match the number of types.');
        }
    }
}
