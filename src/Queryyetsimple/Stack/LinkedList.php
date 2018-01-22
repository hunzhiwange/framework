<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Stack;

use SplDoublyLinkedList;
use InvalidArgumentException;
use Queryyetsimple\Support\Type;

/**
 * 双向链表
 * 在 PHP 双向链表的基础上加上数据类型验证功能，不少业务场景中保证链表中数据一致性
 * 以及链表返回空数据时阻止抛出异常的默认行为
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.21
 * @see http://php.net/manual/zh/class.spldoublylinkedlist.php
 * @version 1.0
 */
class LinkedList extends SplDoublyLinkedList
{

    /**
     * 允许的类型
     *
     * @var array
     */
    protected $type = [];

    /**
     * 构造函数
     *
     * @param mixed $type
     * @return void
     */
    public function __construct($type = null)
    {
        $this->type = is_array($type) ? $type : func_get_args();
    }

    /**
     * {@inheritdoc}
     */
    public function pop()
    {
        if ($this->isEmpty()) {
            return null;
        }
        return parent::pop();
    }

    /**
     * {@inheritdoc}
     */
    public function add($index, $newval)
    {
        $this->validate($newval);
        parent::add($index, $newval);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($index, $newval)
    {
        $this->validate($newval);
        parent::offsetSet($index, $newval);
    }

    /**
     * {@inheritdoc}
     */
    public function push($value)
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
     * 验证类型是否正确遇到错误抛出异常
     *
     * @param mixed $value
     * @return void
     */
    public function validate($value)
    {
        if (! $this->checkType($value)) {
            throw new InvalidArgumentException(sprintf('The linkedlist element type verification failed, and the allowed type is %s.', implode(',', $this->type)));
        }
    }

    /**
     * 验证类型是否正确
     *
     * @param mixed $value
     * @return bool
     */
    protected function checkType($value)
    {
        if (! count($this->type)) {
            return true;
        }

        return Type::varThese($value, $this->type);
    }
}
