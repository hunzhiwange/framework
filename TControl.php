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
namespace Queryyetsimple\Flow;

/**
 * 流程控制复用
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.13
 * @version 1.0
 */
trait TControl
{

    /**
     * 逻辑代码是否处于条件表达式中
     *
     * @var boolean
     */
    protected $booInTControl = false;

    /**
     * 条件表达式是否为真
     *
     * @var boolean
     */
    protected $booTControlIsTrue = false;

    /**
     * 条件语句 ifs
     *
     * @param boolean $booValue
     * @return $this
     */
    public function ifs($booValue = false)
    {
        return $this->setTControl(true, $booValue);
    }

    /**
     * 条件语句 elseIfs
     *
     * @param boolean $booValue
     * @return $this
     */
    public function elseIfs($booValue = false)
    {
        return $this->setTControl(true, $booValue);
    }

    /**
     * 条件语句 elses
     *
     * @return $this
     */
    public function elses()
    {
        return $this->setTControl(true, ! $this->getTControl()[1]);
    }

    /**
     * 条件语句 endIfs
     *
     * @return $this
     */
    public function endIfs()
    {
        return $this->setTControl(false, false);
    }

    /**
     * 设置当前条件表达式状态
     *
     * @param boolean $booInTControl
     * @param boolean $booTControlIsTrue
     * @return void
     */
    protected function setTControl($booInTControl, $booTControlIsTrue)
    {
        $this->booInTControl = $booInTControl;
        $this->booTControlIsTrue = $booTControlIsTrue;
        return $this;
    }

    /**
     * 获取当前条件表达式状态
     *
     * @return array
     */
    protected function getTControl()
    {
        return [
            $this->booInTControl,
            $this->booTControlIsTrue
        ];
    }

    /**
     * 验证一下条件表达式是否通过
     *
     * @return boolean
     */
    protected function checkTControl()
    {
        return $this->booInTControl && ! $this->booTControlIsTrue;
    }

    /**
     * 占位符
     *
     * @param string $strMethod
     * @return boolean
     */
    protected function placeholderTControl($strMethod)
    {
        return in_array($strMethod, [
            'placeholder',
            'foobar'
        ]);
    }
}
