<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\bootstrap\console\command\queue;

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

use queryyetsimple\cache\cache;
use queryyetsimple\console\command;

/**
 * 重启任务
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.06.07
 * @version 1.0
 */
class restart extends command {
    
    /**
     * 命令名字
     *
     * @var string
     */
    protected $strName = 'queue:restart';
    
    /**
     * 命令行描述
     *
     * @var string
     */
    protected $strDescription = 'Restart queue work after done it current job.';
    
    /**
     * 响应命令
     *
     * @return void
     */
    public function handle() {
        cache::sets ( 'queryphp.queue.restart', time () );
        $this->info ( 'Send queue restart signal.' );
    }
}
