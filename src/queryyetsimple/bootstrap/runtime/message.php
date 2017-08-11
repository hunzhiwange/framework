<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
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

use queryyetsimple\log\store;

/**
 * 消息基类
 *
 * @author Xiangmin Liu <635750556@qq.com>
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
            $this->toResponse ( $this->strMessage );
        }
    }
    
    /**
     * 记录日志
     *
     * @param string $strMessage            
     * @return void
     */
    protected function log($strMessage) {
        if ($this->oProject ['option']->get ( 'log\runtime_enabled', false )) {
            $this->oProject ['log']->write ( $strMessage, store::ERROR );
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
    
    /**
     * 格式为 response
     *
     * @param string $sMessage            
     * @return void
     */
    protected function toResponse($sMessage) {
        $intLevel = ob_get_level ();
        ob_start ();
        
        try {
            $this->errorMessage ( $sMessage );
        } 

        catch ( Exceptions $oE ) {
            while ( ob_get_level () > $intLevel ) {
                ob_end_clean ();
            }
            
            throw $oE;
        }
        
        $strContent = ob_get_clean ();
        
        $booStatusCode = property_exists ( $this, 'objException' ) && method_exists ( $this->objException, 'statusCode' );
        
        $this->oProject ['response']->data ( $strContent )->ifs ( $booStatusCode )->code ( $booStatusCode ? $this->objException->statusCode () : null )->endIfs ()->output ();
    }
}