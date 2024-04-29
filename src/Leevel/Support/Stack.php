<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * 栈.
 *
 * - 后进先出
 *
 * @see http://php.net/manual/zh/class.splstack.php
 */
class Stack extends LinkedList
{
    public function push(mixed $value): void
    {
        parent::push($value);
    }

    public function pop(): mixed
    {
        return parent::pop();
    }
}
