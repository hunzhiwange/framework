<?php

declare(strict_types=1);

namespace Leevel\Stack;

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
}
