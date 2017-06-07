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
     * work 命名
     *
     * @param \queryyetsimple\bootstrap\console\command\queue\work $objWork            
     * @return void
     */
    public function workCommand($objWork) {
        $this->objWork = $objWork;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see \PHPQueue\Runner::workJob()
     */
    public function workJob() {
        parent::workJob ();
        $this->objWork->checkRestart ();
    }
}
