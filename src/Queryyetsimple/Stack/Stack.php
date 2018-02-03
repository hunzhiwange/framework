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

use InvalidArgumentException;

/**
 * 栈，后进先出
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.21
 * @link http://php.net/manual/zh/class.splstack.php
 * @version 1.0
 */
class Stack extends LinkedList implements IStackQueue
{

    /**
     * 入栈
     *
     * @param mixed $value
     * @return void
     */
    public function in($value)
    {
        $this->push($value);
    }

    /**
     * 出栈
     *
     * @return mixed
     */
    public function out()
    {
        return $this->pop();
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value)
    {
        if (! $this->checkType($value)) {
            throw new InvalidArgumentException(sprintf('The stack element type verification failed, and the allowed type is %s.', implode(',', $this->arrType)));
        }
    }
}
