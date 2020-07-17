<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     *
     * @var array
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
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function pop()
    {
        if ($this->isEmpty()) {
            return;
        }

        return parent::pop();
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $index
     * @param mixed $newval
     */
    public function add($index, $newval): void
    {
        $this->validate($newval);
        parent::add($index, $newval);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $index
     * @param mixed $newval
     */
    public function offsetSet($index, $newval): void
    {
        $this->validate($newval);
        parent::offsetSet($index, $newval);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $value
     */
    public function push($value): void
    {
        $this->validate($value);
        parent::push($value);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $value
     */
    public function unshift($value): void
    {
        $this->validate($value);
        parent::unshift($value);
    }

    /**
     * 验证类型是否正确遇到错误抛出异常.
     *
     * @param mixed $value
     *
     * @throws \InvalidArgumentException
     */
    public function validate($value): void
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
     *
     * @param mixed $value
     */
    protected function checkType($value): bool
    {
        if (!$this->type) {
            return true;
        }

        return these($value, $this->type);
    }
}

// import fn.
class_exists(these::class);
