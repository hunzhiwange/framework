<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\queue\interfaces;

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

/**
 * 任务接口
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.06.06
 * @version 1.0
 */
interface job {
    
    /**
     * 执行任务
     *
     * @return void
     */
    public function handle();
    
    /**
     * 取得 job 实例
     *
     * @return object
     */
    public function getInstance();
    
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
