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

namespace Leevel\Queue\Runners;

use Exception;
use PHPQueue\Base;
use PHPQueue\Runner as PHPQueueRunner;

/**
 * 基类 Runner.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.12
 *
 * @version 1.0
 */
abstract class Runner extends PHPQueueRunner
{
    /**
     * work 命令.
     *
     * @var \Leevel\Queue\Console\Work
     */
    protected $work;

    /**
     * 消息队列.
     *
     * @var \Leevel\Queue\Queues\IQueue
     */
    protected $queue;

    /**
     * 任务不可用等待时间.
     *
     * @var int
     */
    protected $sleep = 5;

    /**
     * 任务最大尝试次数.
     *
     * @var int
     */
    protected $tries = 0;

    /**
     * work 命名.
     *
     * @param \Leevel\Queue\Console\Work $work
     */
    public function workCommand($work)
    {
        $this->work = $work;
        $this->tries = $work->tries();
        $this->sleep = $work->sleep();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function workJob()
    {
        $sleepTime = self::RUN_USLEEP;
        $newJob = null;

        try {
            $newJob = Base::getJob($this->queue);
        } catch (Exception $e) {
            $this->logger->addError($e->getMessage());

            // 任务不可用等待时间
            $sleepTime = self::RUN_USLEEP * $this->sleep;
        }
        if (empty($newJob)) {
            $this->logger->addNotice('No Job found.');

            // 任务不可用等待时间
            $sleepTime = self::RUN_USLEEP * $this->sleep;
        } else {
            try {
                if (empty($newJob->worker)) {
                    throw new Exception('No worker declared.');
                }

                // 验证任务最大尝试次数
                if ($this->tries > 0 && $newJob->getAttempts() > $this->tries) {
                    return $this->failedJob($newJob);
                }

                if (is_string($newJob->worker)) {
                    $resultData = $this->processWorker($newJob->worker, $newJob);
                } elseif (is_array($newJob->worker)) {
                    $this->logger->addInfo(
                        sprintf(
                            'Running chained new job (%s) with workers',
                            $newJob->job_id
                        ),
                        $newJob->worker
                    );

                    foreach ($newJob->worker as $workerName) {
                        $resultData = $this->processWorker($workerName, $newJob);
                        $newJob->data = $resultData;
                    }
                }

                return $this->updateJob($newJob, $resultData);
            } catch (Exception $e) {
                $this->logger->addError($e->getMessage());
                $this->logger->addInfo(sprintf('Releasing job (%s).', $newJob->job_id));

                // 删除了就不能重新发布
                if (!$newJob->isDeleted()) {
                    $this->queue->releaseJob($newJob->job_id);
                }

                $sleepTime = self::RUN_USLEEP * 1;
            }
        }

        if ($sleepTime) {
            $this->logger->addInfo('Sleeping '.ceil($sleepTime / 1000000).' seconds.');

            usleep($sleepTime);
        }

        // 验证是否需要重启
        $this->checkRestart();
    }

    /**
     * {@inheritdoc}
     */
    protected function beforeLoop()
    {
        if (empty($this->queue_name)) {
            throw new Exception('Queue name is invalid');
        }

        $this->queue = Base::getQueue($this->queue_name);
    }

    /**
     * 记录错误任务
     *
     * @param \Leevel\Queue\Jobs\IJob $job
     */
    protected function failedJob(IJob $job)
    {
        $status = false;

        if (!$job->isDeleted()) {
            try {
                $job->delete();
                $job->failed();
                $job->onError();

                $this->queue->beforeUpdate();
                $this->queue->updateJob($job->job_id, $job->data);

                $status = $this->queue->clearJob($job->job_id);

                $this->queue->afterUpdate();
            } finally {
            }
        }

        return $status;
    }

    /**
     * 更新任务数据.
     *
     * @param \Leevel\Queue\Jobs\IJob $job
     * @param mixed                   $resultData
     *
     * @throws \Exception
     *
     * @return bool|void
     */
    protected function updateJob(IJob $job, $resultData = null)
    {
        $status = false;

        try {
            $this->queue->beforeUpdate();
            $this->queue->updateJob($job->job_id, $resultData);

            $status = $this->queue->clearJob($job->job_id);

            $this->queue->afterUpdate();
        } catch (Exception $e) {
            $this->queue->onError($e);

            // 删除了就不能重新发布
            if (!$job->isDeleted()) {
                $this->queue->releaseJob($job->job_id);
            }

            throw $e;
        }

        return $status;
    }

    /**
     * 验证是否需要重启.
     */
    protected function checkRestart()
    {
        $this->work->checkRestart();
    }
}
