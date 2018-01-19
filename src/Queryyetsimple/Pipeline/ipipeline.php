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
namespace queryyetsimple\pipeline;

/**
 * ipipeline 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.11
 * @version 1.0
 */
interface ipipeline
{

    /**
     * 管道初始化
     *
     * @return $this
     */
    public function reset();

    /**
     * 将传输对象传入管道
     *
     * @param mixed $passed
     * @return $this
     */
    public function send($passed);

    /**
     * 设置管道中的执行工序
     *
     * @param dynamic|array $stage
     * @return $this
     */
    public function through($stage);

    /**
     * 执行管道工序响应结果
     *
     * @param callable $end
     * @return mixed
     */
    public function then(callable $end = null);
}
