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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Queue\Queues;

/**
 * 队列接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.06
 *
 * @version 1.0
 */
interface IQueue
{
    /**
     * 设置消息队列.
     *
     * @param string $queue
     */
    public static function setQueue(string $queue = 'default');

    /**
     * 设置日志路径.
     *
     * @param string $logPath
     */
    public static function logPath(string $logPath);

    /**
     * 添加一个任务
     *
     * @param null|array $newJob
     *
     * @return bool
     */
    public function addJob(?array $newJob = null);

    /**
     * 获取一个任务
     *
     * @param null|string $jobId
     *
     * @return object
     */
    public function getJob(?string $jobId = null);

    /**
     * 更新任务
     *
     * @param null|string $jobId
     * @param null|array  $resultData
     */
    public function updateJob(?string $jobId = null, ?array $resultData = null);

    /**
     * 删除任务
     *
     * @param null|string $jobId
     */
    public function clearJob(?string $jobId = null);

    /**
     * 重新发布任务
     *
     * @param null|string $jobId
     */
    public function releaseJob(?string $jobId = null);

    /**
     * 取得存储连接 key
     * redis:email 表示 redis 邮件队列.
     *
     * @return string
     */
    public function makeSourceKey();
}
