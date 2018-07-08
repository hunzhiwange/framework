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

use PHPQueue\JobQueue;
use PHPQueue\Logger;

/**
 * Queue 消息队列.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.11
 *
 * @version 1.0
 */
abstract class Queue extends JobQueue
{
    /**
     * 队列连接.
     *
     * @var string
     */
    protected $connect;

    /**
     * 默认执行队列.
     *
     * @var string
     */
    protected static $queue = 'default';

    /**
     * 消息队列日志路径.
     *
     * @var string
     */
    protected static $logPath;

    /**
     * 连接句柄.
     *
     * @var resource
     */
    protected $resDataSource;

    /**
     * 连接配置.
     *
     * @var array
     */
    protected $sourceConfig = [];

    /**
     * 队列执行者.
     *
     * @var string
     */
    protected $queueWorker;

    /**
     * 日志对象
     *
     * @var array
     */
    protected $resultLog;

    /**
     * 构造函数.
     */
    public function __construct()
    {
        parent::__construct();

        // 存储队列
        $this->sourceConfig['queue'] = $this->makeSourceKey();

        // 记录日志
        if (self::$logPath) {
            if (!is_dir(self::$logPath)) {
                mkdir(self::$logPath, 0777, true);
            }

            $this->resultLog = Logger::createLogger(
                $this->connect,
                Logger::INFO,
                self::$logPath.'/'.$this->connect.'.log'
            );
        }
    }

    /**
     * 设置消息队列.
     *
     * @param string $queue
     */
    public static function setQueue(string $queue = 'default')
    {
        static::$queue = $queue;
    }

    /**
     * 设置日志路径.
     *
     * @param string $logPath
     */
    public static function logPath(string $logPath)
    {
        static::$logPath = $logPath;
    }

    /**
     * 添加一个任务
     *
     * @param null|array $newJob
     *
     * @return bool
     */
    public function addJob(?array $newJob = null): bool
    {
        $formattedData = [
            'worker' => $this->queueWorker,
            'data'   => $newJob,
        ];

        $this->resDataSource->add($formattedData);

        return true;
    }

    /**
     * 获取一个任务
     *
     * @param null|string $jobId
     *
     * @return object
     */
    public function getJob(?string $jobId = null)
    {
        $data = $this->resDataSource->get();

        if (!class_exists($job = '\Leevel\Queue\jobs\\'.$this->connect)) {
            $job = '\PHPQueue\Job';
        }

        $nextJob = new $job(
            $data,
            $this->resDataSource->last_job_id,
            static::$queue
        );

        $this->last_job_id = $this->resDataSource->last_job_id;

        return $nextJob;
    }

    /**
     * 更新任务
     *
     * @param null|string $jobId
     * @param null|array  $arrResultData
     */
    public function updateJob($jobId = null, $arrResultData = null)
    {
        if (!$this->resultLog) {
            return;
        }

        $this->arrResultLog->addInfo(
            'Result: ID='.$jobId,
            $arrResultData
        );
    }

    /**
     * 删除任务
     *
     * @param null|string $jobId
     */
    public function clearJob($jobId = null)
    {
        $this->resDataSource->clear($jobId);
    }

    /**
     * 重新发布任务
     *
     * @param null|string $jobId
     */
    public function releaseJob($jobId = null)
    {
        $this->resDataSource->release($jobId);
    }

    /**
     * 取得存储连接 key
     * redis:email 表示 redis 邮件队列.
     *
     * @return string
     */
    public function makeSourceKey()
    {
        return $this->connect.':'.static::$queue;
    }
}
