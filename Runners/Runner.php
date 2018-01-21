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
namespace Queryyetsimple\Queue\Runners;

use Exception;
use PHPQueue\{
    Base,
    Runner as PHPQueueRunner
};

/**
 * 基类 Runner
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.12
 * @version 1.0
 */
abstract class Runner extends PHPQueueRunner
{

    /**
     * work 命令
     *
     * @var \Queryyetsimple\Queue\Console\Work
     */
    protected $objWork;

    /**
     * 消息队列
     *
     * @var \Queryyetsimple\Queue\Queues\IQueue
     */
    protected $objQueue;

    /**
     * 任务不可用等待时间
     *
     * @var int
     */
    protected $intSleep = 5;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    protected $intTries = 0;

    /**
     * work 命名
     *
     * @param \Queryyetsimple\Queue\Console\Work $objWork
     * @return void
     */
    public function workCommand($objWork)
    {
        $this->objWork = $objWork;
        $this->intTries = $objWork->tries();
        $this->intSleep = $objWork->sleep();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function workJob()
    {
        $nSleepTime = self::RUN_USLEEP;
        $obJNewJob = null;
        try {
            $obJNewJob = Base::getJob($this->objQueue);
        } catch (Exception $oEx) {
            $this->logger->addError($oEx->getMessage());

            // 任务不可用等待时间
            $nSleepTime = self::RUN_USLEEP * $this->intSleep;
        }
        if (empty($obJNewJob)) {
            $this->logger->addNotice("No Job found.");

            // 任务不可用等待时间
            $nSleepTime = self::RUN_USLEEP * $this->intSleep;
        } else {
            try {
                if (empty($obJNewJob->worker)) {
                    throw new Exception("No worker declared.");
                }

                // 验证任务最大尝试次数
                if ($this->intTries > 0 && $obJNewJob->getAttempts() > $this->intTries) {
                    return $this->failedJob($obJNewJob);
                }

                if (is_string($obJNewJob->worker)) {
                    $arrResultData = $this->processWorker($obJNewJob->worker, $obJNewJob);
                } elseif (is_array($obJNewJob->worker)) {
                    $this->logger->addInfo(sprintf("Running chained new job (%s) with workers", $obJNewJob->job_id), $obJNewJob->worker);
                    foreach ($obJNewJob->worker as $strWorkerName) {
                        $arrResultData = $this->processWorker($strWorkerName, $obJNewJob);
                        $obJNewJob->data = $arrResultData;
                    }
                }

                return $this->updateJob($obJNewJob, $arrResultData);
            } catch (Exception $oEx) {
                $this->logger->addError($oEx->getMessage());
                $this->logger->addInfo(sprintf('Releasing job (%s).', $obJNewJob->job_id));

                // 删除了就不能重新发布
                if (! $obJNewJob->isDeleted()) {
                    $this->objQueue->releaseJob($obJNewJob->job_id);
                }

                $nSleepTime = self::RUN_USLEEP * 1;
            }
        }

        if ($nSleepTime) {
            $this->logger->addInfo('Sleeping ' . ceil($nSleepTime / 1000000) . ' seconds.');
            usleep($nSleepTime);
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
        $this->objQueue = Base::getQueue($this->queue_name);
    }

    /**
     * 记录错误任务
     *
     * @param \Queryyetsimple\Queue\jobs\ijob $objJob
     * @return void
     */
    protected function failedJob($objJob)
    {
        $booStatus = false;

        if (! $objJob->isDeleted()) {
            try {
                $objJob->delete();
                $objJob->failed();
                $objJob->onError();
                $this->objQueue->beforeUpdate();
                $this->objQueue->updateJob($objJob->job_id, $objJob->data);
                $booStatus = $this->objQueue->clearJob($objJob->job_id);
                $this->objQueue->afterUpdate();
            } finally {
            }
        }

        return $booStatus;
    }

    /**
     * 更新任务数据
     *
     * @param \Queryyetsimple\Queue\jobs\ijob $objJob
     * @param mixed $mixResultData
     * @return bool|void
     * @throws \Exception
     */
    protected function updateJob($objJob, $mixResultData = null)
    {
        $booStatus = false;
        try {
            $this->objQueue->beforeUpdate();
            $this->objQueue->updateJob($obJJob->job_id, $mixResultData);
            $booStatus = $this->objQueue->clearJob($obJJob->job_id);
            $this->objQueue->afterUpdate();
        } catch (Exception $oEx) {
            $this->objQueue->onError($oEx);

            // 删除了就不能重新发布
            if (! $obJJob->isDeleted()) {
                $this->objQueue->releaseJob($obJJob->job_id);
            }

            throw $oEx;
        }

        return $booStatus;
    }

    /**
     * 验证是否需要重启
     *
     * @return void
     */
    protected function checkRestart()
    {
        $this->objWork->checkRestart();
    }
}
