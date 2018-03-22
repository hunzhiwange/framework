<?php
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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Stack;

/**
 * 栈和队列接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.11
 * @version 1.0
 */
interface IStackQueue
{

    /**
     * 入
     *
     * @param mixed $value
     * @return void
     */
    public function in($value);

    /**
     * 出
     *
     * @return mixed
     */
    public function out();

    /**
     * 验证类型是否正确遇到错误抛出异常
     *
     * @param mixed $value
     * @return void
     */
    public function validate($value);
}
