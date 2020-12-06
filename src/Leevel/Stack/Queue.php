<?php

declare(strict_types=1);

namespace Leevel\Stack;

use InvalidArgumentException;

/**
 * 队列.
 *
 * - 先进先出
 *
 * @see http://php.net/manual/zh/class.splqueue.php
 */
class Queue extends LinkedList
{
    /**
     * 入对.
     */
    public function enqueue(mixed $value): void
    {
        $this->push($value);
    }

    /**
     * 出对.
     */
    public function dequeue(): mixed
    {
        return $this->shift();
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
                'The queue element type verification failed, and the allowed type is %s.',
                implode(',', $this->type)
            );

            throw new InvalidArgumentException($e);
        }
    }
}
