<?php
// [$QueryPHP] A PHP Framework For Simple As Free As Wind. <Query Yet Simple>
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

use queryyetsimple\debug\dump;
use queryyetsimple\filesystem\filesystem;

/**
 * 异常消息
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.04
 * @version 1.0
 */
class exception extends message {
    
    /**
     * 异常组件
     *
     * @var object
     */
    private $objException;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\bootstrap\project $oProject            
     * @param object $objException            
     * @return void
     */
    public function __construct($oProject, $objException) {
        $this->oProject = $oProject;
        $this->objException = $objException;
        $this->strMessage = "[{$this->objException->getCode ()}] {$this->objException->getMessage ()} " . basename ( $this->objException->getFile () ) . __ ( " 第 %d 行", $this->objException->getLine () );
    }
    
    /**
     * 错误消息执行入口
     *
     * @return void
     */
    public function run() {
        $this->log ( $this->strMessage );
        $this->errorMessage ( $this->format ( $this->objException ) );
    }
    
    /**
     * 输入异常消息
     *
     * @param mixed $mixError            
     * @return void
     */
    protected function errorMessage($mixError) {
        if (! is_array ( $mixError )) {
            $mixError = ( array ) $mixError;
        }
        
        // 否则定向到错误页面
        if (PHP_SAPI != 'cli' && $this->oProject ['option'] ['show_exception_redirect'] && ! env ( 'app_debug' )) {
            $this->oProject ['router']->urlRedirect ( $this->oProject ['router']->url ( $this->oProject ['option'] ['show_exception_redirect'] ) );
        } else {
            if (! $this->oProject ['option']->get ( 'show_exception_show_message', true ) && $this->oProject ['option']->get ( 'show_exception_default_message' )) {
                $mixError ['message'] = $this->oProject ['option']->get ( 'show_exception_default_message' );
            }
            
            // 包含异常页面模板
            if (PHP_SAPI == 'cli') {
                echo $mixError ['message'];
            } else {
                if ($this->oProject ['option']->get ( 'show_exception_template' ) && is_file ( $this->oProject ['option']->get ( 'show_exception_template' ) )) {
                    include $this->oProject ['option']->get ( 'show_exception_template' );
                } else {
                    include dirname ( __DIR__ ) . '/template/exception.php';
                }
            }
        }
    }
    
    /**
     * 格式化消息
     *
     * @param object $oException            
     * @return array
     */
    private function format($oException) {
        // 返回消息
        $arrError = [ ];
        
        // 反转一下
        $arrTrace = array_reverse ( $oException->getTrace () );
        
        // 调试消息
        $sTraceInfo = '';
        if (env ( 'app_debug' )) {
            foreach ( $arrTrace as $intKey => $arrVal ) {
                // 参数处理
                $arrVal ['class'] = isset ( $arrVal ['class'] ) ? $arrVal ['class'] : '';
                $arrVal ['type'] = isset ( $arrVal ['type'] ) ? $arrVal ['type'] : '';
                $arrVal ['function'] = isset ( $arrVal ['function'] ) ? $arrVal ['function'] : '';
                $arrVal ['file'] = isset ( $arrVal ['file'] ) ? filesystem::tidyPathLinux ( $arrVal ['file'] ) : '';
                $arrVal ['line'] = isset ( $arrVal ['line'] ) ? $arrVal ['line'] : '';
                $arrVal ['args'] = isset ( $arrVal ['args'] ) ? $arrVal ['args'] : '';
                
                // 参数格式化组装
                $sArgsInfo = $sArgsInfoDetail = '';
                if (is_array ( $arrVal ['args'] )) {
                    foreach ( $arrVal ['args'] as $intArgsKey => $mixArgsVal ) {
                        // 概要参数
                        $sArgsInfo .= ($intArgsKey !== 0 ? ', ' : '') . (is_scalar ( $mixArgsVal ) ? strip_tags ( var_export ( $mixArgsVal, true ) ) : gettype ( $mixArgsVal ));
                        
                        // 详细参数值
                        ob_start ();
                        dump::dump ( $mixArgsVal );
                        $sArgsInfoDetail .= '<div class="queryphp-message-argstitle">Args ' . ($intArgsKey + 1) . '</div><div class="queryphp-message-args">' . ob_get_contents () . '</div>';
                        ob_end_clean ();
                    }
                }
                
                // 调试信息
                $sTraceInfo .= "<li><a " . ($sArgsInfoDetail ? "data-toggle=\"queryphp-message-argsline-{$intKey}\" style=\"cursor: pointer;\"" : '') . "><span>#{$arrVal['line']}</span> {$arrVal['file']} - {$arrVal['class']}{$arrVal['type']}{$arrVal['function']}( {$sArgsInfo} )</a>
                " . ($sArgsInfoDetail ? "<div class=\"queryphp-message-argsline-{$intKey}\" style=\"display:none;\">
                {$sArgsInfoDetail}
                </div>" : '') . "
                </li>";
                
                unset ( $sArgsInfo, $sArgsInfoDetail );
            }
            $arrError ['trace'] = $sTraceInfo;
            unset ( $sTraceInfo );
        }
        
        // 调试消息
        $arrError ['message'] = $oException->getMessage ();
        $arrError ['type'] = isset ( $arrVal ['type'] ) ? $arrVal ['type'] : '';
        $arrError ['class'] = isset ( $arrTrace ['0'] ['class'] ) ? $arrTrace ['0'] ['class'] : '';
        $arrError ['code'] = $oException->getCode ();
        $arrError ['function'] = isset ( $arrTrace ['0'] ['function'] ) ? $arrTrace ['0'] ['function'] : '';
        $arrError ['line'] = $oException->getLine ();
        $arrError ['excetion_type'] = get_class ( $oException );
        
        return $arrError;
    }
}