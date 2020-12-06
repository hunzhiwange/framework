<?php

declare(strict_types=1);

namespace Leevel\Stack;

use InvalidArgumentException;

/**
 * 栈.
 *
 * - 后进先出
 *
 * @see http://php.net/manual/zh/class.splstack.php
 */
class Stack extends LinkedList
{
    /**
     * {@inheritDoc}
     */
    public function push(mixed $value): void
    {
        parent::push($value);
    }

    /**
     * {@inheritDoc}
     */
    public function pop(): mixed
    {
        return parent::pop();
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function validate($value): void
    {
        if (!$this->checkType($value)) {
            $e = sprintf(
                'The stack element type verification failed, and the allowed type is %s.',
                implode(',', $this->type)
            );

            throw new InvalidArgumentException($e);
        }
    }
}
