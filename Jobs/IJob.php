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
namespace Queryyetsimple\Queue\Jobs;

/**
 * 任务接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.06
 * @version 1.0
 */
interface IJob
{

    /**
     * 执行任务
     *
     * @return void
     */
    public function handle();

    /**
     * 调用任务的失败方法
     *
     * @return void
     */
    public function failed();

    /**
     * 取得 job 名字
     *
     * @return string
     */
    public function getName();

    /**
     * 取得 job 数据
     *
     * @return string
     */
    public function getData();

    /**
     * 返回任务执行次数
     *
     * @return int
     */
    public function getAttempts();

    /**
     * 获取任务所属的消息队列
     *
     * @return string
     */
    public function getQueue();

    /**
     * 取得 worker
     *
     * @return string
     */
    public function getWorker();

    /**
     * 取得 job_id
     *
     * @return string
     */
    public function getJobId();
}
