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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Stack;

use InvalidArgumentException;
use function Leevel\Support\Type\type_these;
use Leevel\Support\Type\type_these;
use SplDoublyLinkedList;

/**
 * 双向链表
 * 在 PHP 双向链表的基础上加上数据类型验证功能，不少业务场景中保证链表中数据一致性
 * 以及链表返回空数据时阻止抛出异常的默认行为.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.21
 * @see http://php.net/manual/zh/class.spldoublylinkedlist.php
 *
 * @version 1.0
 */
class LinkedList extends SplDoublyLinkedList
{
    /**
     * 允许的类型.
     *
     * @var array
     */
    protected $type = [];

    /**
     * 构造函数.
     *
     * @param array $type
     */
    public function __construct(array $type = null)
    {
        if ($type) {
            $this->type = $type;
        }
    }

    /**
     * {@inheritdoc}
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
     */
    public function add($index, $newval): void
    {
        $this->validate($newval);

        parent::add($index, $newval);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($index, $newval): void
    {
        $this->validate($newval);

        parent::offsetSet($index, $newval);
    }

    /**
     * {@inheritdoc}
     */
    public function push($value): void
    {
        $this->validate($value);

        parent::push($value);
    }

    /**
     * {@inheritdoc}
     */
    public function unshift($value)
    {
        $this->validate($value);

        parent::unshift($value);
    }

    /**
     * 验证类型是否正确遇到错误抛出异常.
     *
     * @param mixed $value
     */
    public function validate($value): void
    {
        if (!$this->checkType($value)) {
            $e = sprintf('The linkedlist element type verification failed, and the allowed type is %s.',
                implode(',', $this->type)
            );

            throw new InvalidArgumentException($e);
        }
    }

    /**
     * 验证类型是否正确.
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function checkType($value): bool
    {
        if (!$this->type) {
            return true;
        }

        return type_these($value, $this->type);
    }
}

fns(type_these::class);
