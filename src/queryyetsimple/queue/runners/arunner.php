<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\queue\runners;

<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

use Exception;
use PHPQueue\Base;
use PHPQueue\Runner as PHPQueueRunner;

/**
 * 基类 runner
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.12
 * @version 1.0
 */
abstract class arunner extends PHPQueueRunner
{

    /**
     * work 命令
     *
     * @var \queryyetsimple\queue\console\work
     */
    protected $objWork;

    /**
     * 消息队列
     *
     * @var \queryyetsimple\queue\queues\iqueue
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
     * @param \queryyetsimple\queue\console\work $objWork
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
     * (non-PHPdoc)
     *
     * @see \PHPQueue\Runner::workJob()
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
     * (non-PHPdoc)
     *
     * @see \PHPQueue\Runner::beforeLoop()
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
     * @param \queryyetsimple\queue\jobs\ijob $objJob
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
     * @param \queryyetsimple\queue\jobs\ijob $objJob
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
