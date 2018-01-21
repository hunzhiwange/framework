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
namespace Queryyetsimple\Queue\Queues;

/**
 * 队列接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.06
 * @version 1.0
 */
interface IQueue
{

    /**
     * 设置消息队列
     *
     * @param string $strQueue
     * @return void
     */
    public static function setQueue($strQueue = 'default');

    /**
     * 设置日志路径
     *
     * @param string $strLogPath
     * @return void
     */
    public static function logPath($strLogPath);

    /**
     * 添加一个任务
     *
     * @param array|null $arrNewJob
     * @return boolean
     */
    public function addJob($arrNewJob = null);

    /**
     * 获取一个任务
     *
     * @param string|null $strJobId
     * @return object
     */
    public function getJob($strJobId = null);

    /**
     * 更新任务
     *
     * @param string|null $strJobId
     * @param array|null $arrResultData
     * @return void
     */
    public function updateJob($strJobId = null, $arrResultData = null);

    /**
     * 删除任务
     *
     * @param string|null $strJobId
     * @return void
     */
    public function clearJob($strJobId = null);

    /**
     * 重新发布任务
     *
     * @param string|null $strJobId
     * @return void
     */
    public function releaseJob($strJobId = null);

    /**
     * 取得存储连接 key
     * redis:email 表示 redis 邮件队列
     *
     * @return string
     */
    public function makeSourceKey();
}
