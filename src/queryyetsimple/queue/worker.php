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

use Clio\Console;
use PHPQueue\Worker as PHPQueueWorker;
use queryyetsimple\queue\interfaces\worker as interfaces_worker;

/**
 * 基类 worker
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.11
 * @version 1.0
 */
abstract class worker extends PHPQueueWorker implements interfaces_worker {
    
    /**
     * 运行任务
     *
     * @param \PHPQueue\Job $objJob            
     * @return void
     */
    public function runJob($objJob) {
        parent::runJob ( $objJob );
        
        $this->formatMessage ( sprintf ( 'Trying do run job %s.', $objJob->getName () ) );
        
        $objJob->handle ();
        
        $this->formatMessage ( sprintf ( 'Job %s is done.' . "", $objJob->getName () ) );
        $this->formatMessage ( 'Starting the next. ' );
        
        $this->result_data = $objJob->data;
    }
    
    /**
     * 格式化输出消息
     *
     * @param string $strMessage            
     * @return string
     */
    protected function formatMessage($strMessage) {
        Console::stdout ( sprintf ( '[%s]', date ( 'H:i:s' ) ) . $strMessage . PHP_EOL );
    }
}
