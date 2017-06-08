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

use PHPQueue\Job as PHPQueueJob;
use queryyetsimple\queue\interfaces\job as interfaces_job;

/**
 * 基类 job
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.12
 * @version 1.0
 */
abstract class job extends PHPQueueJob implements interfaces_job {
    
    /**
     * 任务实例
     *
     * @var object
     */
    protected $objInstance;
    
    /**
     * 任务所属的消息队列
     *
     * @var string
     */
    protected $strQueue;
    
    /**
     * 任务是否被删除
     *
     * @var boolean
     */
    protected $booDeleted = false;
    
    /**
     * 构造函数
     *
     * @param array $arrData            
     * @param string $strJobId            
     * @param string $strQueue            
     * @return void
     */
    public function __construct($arrData = null, $strJobId = null, $strQueue = 'default') {
        parent::__construct ( $arrData, $strJobId );
        $this->strQueue = $strQueue;
        $this->initialization ();
    }
    
    /**
     * 执行任务
     *
     * @return void
     */
    public function handle() {
        list ( $strJob, $strMethod ) = $this->parseString ( $this->getName () );
        $this->objInstance = $this->getJob ( $strJob );
        
        $strMethod = method_exists ( $this->objInstance, $strMethod ) ? $strMethod : ($strMethod != 'handle' && method_exists ( $this->objInstance, 'handle' ) ? 'handle' : 'run');
        
        $arrArgs = $this->getData ();
        array_unshift ( $arrArgs, $this );
        call_user_func_array ( [ 
                project (),
                'call' 
        ], [ 
                [ 
                        $this->objInstance,
                        $strMethod 
                ],
                $arrArgs 
        ] );
    }
    
    /**
     * 标识任务删除
     *
     * @return void
     */
    public function delete() {
        $this->booDeleted = true;
    }
    
    /**
     * 任务是否被删除
     *
     * @return boolean
     */
    public function isDeleted() {
        return $this->booDeleted;
    }
    
    /**
     * 取得 job 实例
     *
     * @return object
     */
    public function getInstance() {
        return $this->objInstance;
    }
    
    /**
     * 取得 job 名字
     *
     * @return string
     */
    public function getName() {
        return $this->data ['job'];
    }
    
    /**
     * 取得 job 数据
     *
     * @return string
     */
    public function getData() {
        return $this->data ['data'];
    }
    
    /**
     * 返回任务执行次数
     *
     * @return int
     */
    public function getAttempts() {
        return $this->data ['attempts'];
    }
    
    /**
     * 获取任务所属的消息队列
     *
     * @return string
     */
    public function getQueue() {
        return $this->strQueue;
    }
    
    /**
     * 取得 worker
     *
     * @return string
     */
    public function getWorker() {
        return $this->worker;
    }
    
    /**
     * 取得 job_id
     *
     * @return string
     */
    public function getJobId() {
        return $this->job_id;
    }
    
    /**
     * 分析任务名字
     *
     * @param string $strJob            
     * @return array
     */
    protected function parseString($strJob) {
        $strJob = explode ( '@', $strJob );
        return ! empty ( $strJob [1] ) ? $strJob : [ 
                $strJob [0],
                'handle' 
        ];
    }
    
    /**
     * 取得任务实例
     *
     * @param string $strJob            
     * @return object
     */
    protected function getJob($strJob) {
        return project ()->make ( $strJob );
    }
    
    /**
     * 初始化
     *
     * @return void
     */
    protected function initialization() {
        if (! isset ( $this->data ['data'] ))
            $this->data ['data'] = [ ];
        
        if (! isset ( $this->data ['attempts'] ))
            $this->data ['attempts'] = 1;
    }
}
