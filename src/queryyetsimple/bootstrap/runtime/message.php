<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\bootstrap\runtime;

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
 * 消息基类
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.04
 * @version 1.0
 */
abstract class message {
    
    /**
     * 返回项目容器
     *
     * @var \queryyetsimple\bootstrap\project
     */
    protected $oProject;
    
    /**
     * 错误消息
     *
     * @var string
     */
    protected $strMessage;
    
    /**
     * 错误消息执行入口
     *
     * @return void
     */
    public function run() {
        if ($this->strMessage) {
            $this->log ( $this->strMessage );
            $this->errorMessage ( $this->strMessage );
        }
    }
    
    /**
     * 记录日志
     *
     * @param string $strMessage            
     * @return void
     */
    protected function log($strMessage) {
        if ($this->oProject ['option']->get ( 'log_error_enabled', false )) {
            $this->oProject ['log']->run ( $strMessage, 'error' );
        }
    }
    
    /**
     * 输出一个致命错误
     *
     * @param string $sMessage            
     * @return void
     */
    protected function errorMessage($sMessage) {
        require_once dirname ( __DIR__ ) . '/template/error.php';
    }
}