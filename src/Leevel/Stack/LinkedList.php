<?php

declare(strict_types=1);

namespace Leevel\Stack;

use InvalidArgumentException;
use function Leevel\Support\Type\these;
use Leevel\Support\Type\these;
use SplDoublyLinkedList;

/**
 * 双向链表.
 *
 * - 在 PHP 双向链表的基础上加上数据类型验证功能，不少业务场景中保证链表中数据一致性.
 * - 阻止链表返回空数据时抛出异常的默认行为.
 *
 * @see http://php.net/manual/zh/class.spldoublylinkedlist.php
 */
class LinkedList extends SplDoublyLinkedList
{
    /**
     * 允许的类型.
     */
    protected array $type = [];

    /**
     * 构造函数.
     */
    public function __construct(?array $type = null)
    {
        if ($type) {
            $this->type = $type;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function pop(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        return parent::pop();
    }

    /**
     * {@inheritDoc}
     */
    public function add(mixed $index, mixed $newval): void
    {
        $this->validate($newval);
        parent::add($index, $newval);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet(mixed $index, mixed $newval): void
    {
        $this->validate($newval);
        parent::offsetSet($index, $newval);
    }

    /**
     * {@inheritDoc}
     */
    public function push(mixed $value): void
    {
        $this->validate($value);
        parent::push($value);
    }

    /**
     * {@inheritDoc}
     */
    public function unshift(mixed $value): void
    {
        $this->validate($value);
        parent::unshift($value);
    }

    /**
     * 验证类型是否正确遇到错误抛出异常.
     *
     * @throws \InvalidArgumentException
     */
    public function validate(mixed $value): void
    {
        if (!$this->checkType($value)) {
            $e = sprintf(
                'The linkedlist element type verification failed, and the allowed type is %s.',
                implode(',', $this->type)
            );

            throw new InvalidArgumentException($e);
        }
    }

    /**
     * 验证类型是否正确.
     */
    protected function checkType(mixed $value): bool
    {
        if (!$this->type) {
            return true;
        }

        return these($value, $this->type);
    }
}

// import fn.
class_exists(these::class);
