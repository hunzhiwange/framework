<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\pipeline\interfaces;

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
 * pipeline 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.11
 * @version 1.0
 */
interface pipeline {
    
    /**
     * 将传输对象传入管道
     *
     * @param mixed $mixPassed            
     * @return $this
     */
    public function send($mixPassed);
    
    /**
     * 设置管道中的执行工序
     *
     * @param dynamic|array $mixStages            
     * @return $this
     */
    public function through($mixStages /* args */ );
    
    /**
     * 添加一道工序
     *
     * @param callable $calStage            
     * @return $this
     */
    public function stage(callable $calStage);
    
    /**
     * 执行管道工序响应结果
     *
     * @param callable $calEnd            
     * @return mixed
     */
    public function then(callable $calEnd);
    
    /**
     * 执行管道工序
     *
     * @return mixed
     */
    public function run();
}
