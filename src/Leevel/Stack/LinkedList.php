<?php

declare(strict_types=1);

namespace Leevel\Stack;

use Leevel\Support\Type\These;
use SplDoublyLinkedList;
use UnexpectedValueException;

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
    protected array $types = [];

    /**
     * 构造函数.
     */
    public function __construct(array $types = [])
    {
        if ($types) {
            $this->types = $types;
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
     * @throws \UnexpectedValueException
     */
    public function validate(mixed $value): void
    {
        if (!$this->checkType($value)) {
            $e = sprintf('The element type must be one of the following `%s`.', implode(',', $this->types));

            throw new UnexpectedValueException($e);
        }
    }

    /**
     * 验证类型是否正确.
     */
    protected function checkType(mixed $value): bool
    {
        if (!$this->types) {
            return true;
        }

        return These::handle($value, $this->types);
    }
}
