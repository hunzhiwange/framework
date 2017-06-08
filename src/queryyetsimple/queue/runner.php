<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\queue;

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
use queryyetsimple\queue\interfaces\runner as interfaces_runner;

/**
 * 基类 runner
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.12
 * @version 1.0
 */
abstract class runner extends PHPQueueRunner implements interfaces_runner {
    
    /**
     * work 命令
     *
     * @var \queryyetsimple\bootstrap\console\command\queue\work
     */
    protected $objWork = null;
    
    /**
     * 消息队列
     *
     * @var \queryyetsimple\queue\interfaces\queue
     */
    protected $objQueue = null;
    
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
     * @param \queryyetsimple\bootstrap\console\command\queue\work $objWork            
     * @return void
     */
    public function workCommand($objWork) {
        $this->objWork = $objWork;
        $this->intTries = $objWork->tries ();
        $this->intSleep = $objWork->sleep ();
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see \PHPQueue\Runner::workJob()
     */
    public function workJob() {
        // 重写继承类
        $sleepTime = self::RUN_USLEEP;
        $newJob = null;
        try {
            $newJob = Base::getJob ( $this->objQueue );
        } catch ( Exception $ex ) {
            $this->logger->addError ( $ex->getMessage () );
            
            // 任务不可用等待时间
            $sleepTime = self::RUN_USLEEP * $this->intSleep;
        }
        if (empty ( $newJob )) {
            $this->logger->addNotice ( "No Job found." );
            
            // 任务不可用等待时间
            $sleepTime = self::RUN_USLEEP * $this->intSleep;
        } else {
            try {
                if (empty ( $newJob->worker )) {
                    throw new Exception ( "No worker declared." );
                }
                
                // 验证任务最大尝试次数
                if ($this->intTries > 0 && $newJob->getAttempts () > $this->intTries) {
                    return $this->failed ( $newJob );
                }
                
                if (is_string ( $newJob->worker )) {
                    $result_data = $this->processWorker ( $newJob->worker, $newJob );
                } elseif (is_array ( $newJob->worker )) {
                    $this->logger->addInfo ( sprintf ( "Running chained new job (%s) with workers", $newJob->job_id ), $newJob->worker );
                    foreach ( $newJob->worker as $worker_name ) {
                        $result_data = $this->processWorker ( $worker_name, $newJob );
                        $newJob->data = $result_data;
                    }
                }
                
                return Base::updateJob ( $this->objQueue, $newJob->job_id, $result_data );
            } catch ( Exception $ex ) {
                $this->logger->addError ( $ex->getMessage () );
                $this->logger->addInfo ( sprintf ( 'Releasing job (%s).', $newJob->job_id ) );
                $this->objQueue->releaseJob ( $newJob->job_id );
                $sleepTime = self::RUN_USLEEP * 5;
            }
        }
        $this->logger->addInfo ( 'Sleeping ' . ceil ( $sleepTime / 1000000 ) . ' seconds.' );
        usleep ( $sleepTime );
        
        // 验证是否需要重启
        $this->checkRestart ();
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see \PHPQueue\Runner::beforeLoop()
     */
    protected function beforeLoop() {
        if (empty ( $this->queue_name )) {
            throw new Exception ( 'Queue name is invalid' );
        }
        $this->objQueue = Base::getQueue ( $this->queue_name );
    }
    
    /**
     * 记录错误任务
     *
     * @param \queryyetsimple\queue\interfaces\job $objJob            
     * @return void
     */
    protected function failed($objJob) {
        $booStatus = false;
        
        if (! $objJob->isDeleted ()) {
            try {
                $objJob->delete ();
                $objJob->onError ();
                $this->objQueue->beforeUpdate ();
                $this->objQueue->updateJob ( $objJob->job_id, $objJob->data );
                $booStatus = $this->objQueue->clearJob ( $objJob->job_id );
                $this->objQueue->afterUpdate ();
            } finally {
            }
        }
        
        return $booStatus;
    }
    
    /**
     * 验证是否需要重启
     *
     * @return void
     */
    protected function checkRestart() {
        $this->objWork->checkRestart ();
    }
}
