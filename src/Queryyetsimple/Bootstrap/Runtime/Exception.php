<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 * 
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Bootstrap\Runtime;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use Exception as Exceptions;
use Queryyetsimple\{
    Filesystem\Fso,
    Support\Debug\Dump
};

/**
 * 异常消息
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.04
 * @version 1.0
 */
class Exception extends Message
{
    
    /**
     * 异常组件
     *
     * @var object
     */
    protected $objException;
    
    /**
     * 构造函数
     *
     * @param \Queryyetsimple\Bootstrap\Project $oProject
     * @param object $objException
     * @return void
     */
    public function __construct($oProject, $objException)
    {
        $this->oProject = $oProject;
        $this->objException = $objException;
        $this->strMessage = "[{$this->objException->getCode()}] {$this->objException->getMessage()} " . basename($this->objException->getFile()) . sprintf(" line %d", $this->objException->getLine());
    }
    
    /**
     * 错误消息执行入口
     *
     * @return void
     */
    public function run()
    {
        $this->log($this->strMessage);
        $this->toResponse($this->oProject['option']['default_response'] == 'api' ? $this->formatForApi($this->objException) : $this->format($this->objException));
    }
    
    /**
     * 输入异常消息
     *
     * @param mixed $mixError
     * @return void
     */
    protected function errorMessage($mixError)
    {
        if (! is_array($mixError)) {
            $mixError = ( array ) $mixError;
        }

        if ($this->oProject['option']['default_response'] == 'api') {
            return $mixError;
        }
        
        // 否则定向到错误页面
        if (PHP_SAPI != 'cli' && $this->oProject['option']['show_exception_redirect'] && ! $this->oProject['option']['debug']) {
            $this->oProject['router']->urlRedirect($this->oProject['router']->url($this->oProject['option']['show_exception_redirect']));
        } else {
            if (! $this->oProject['option']->get('show_exception_show_message', true) && $this->oProject['option']->get('show_exception_default_message')) {
                $mixError['message'] = $this->oProject['option']->get('show_exception_default_message');
            }
            
            // 包含异常页面模板
            if (PHP_SAPI == 'cli') {
                echo $mixError['message'];
            } else {
                if ($this->oProject['option']->get('show_exception_template') && is_file($this->oProject['option']->get('show_exception_template'))) {
                    $exceptionpath = $this->oProject['option']->get('show_exception_template');
                } else {
                    $exceptionpath = $this->oProject->pathSystem('exception');
                }
                
                if (! is_file($exceptionpath)) {
                    exit(sprintf('Exception file %s is not exits.', $exceptionpath));
                }

                require_once $exceptionpath;
            }
        }
    }
    
    /**
     * 格式化消息
     *
     * @param object $oException
     * @return array
     */
    protected function format($oException)
    {
        // 返回消息
        $arrError = [];
        
        // 反转一下
        $arrTrace = array_reverse($oException->getTrace());
        
        // 调试消息
        $sTraceInfo = '';
        if ($this->oProject['option']['debug']) {
            foreach ($arrTrace as $intKey => $arrVal) {
                // 参数处理
                $arrVal['class'] = $arrVal['class'] ?? '';
                $arrVal['type'] = $arrVal['type'] ?? '';
                $arrVal['function'] = $arrVal['function'] ?? '';
                $arrVal['file'] = isset($arrVal['file']) ? Fso::tidyPathLinux($arrVal['file']) : '';
                $arrVal['line'] = $arrVal['line'] ?? '';
                $arrVal['args'] = $arrVal['args'] ?? '';
                
                // 参数格式化组装
                $sArgsInfo = $sArgsInfoDetail = '';
                if (is_array($arrVal['args'])) {
                    foreach ($arrVal['args'] as $intArgsKey => $mixArgsVal) {
                        // 概要参数
                        $sArgsInfo .= ($intArgsKey !== 0 ? ', ' : '') . (is_scalar($mixArgsVal) ? strip_tags(var_export($mixArgsVal, true)) : gettype($mixArgsVal));
                        
                        // 详细参数值
                        ob_start();
                        dump::varDump($this->formatArgs($mixArgsVal));
                        $sArgsInfoDetail .= '<div class="queryphp-message-argstitle">Args ' . ($intArgsKey + 1) . '</div><div class="queryphp-message-args">' . ob_get_contents() . '</div>';
                        ob_end_clean();
                    }
                }
                
                // 调试信息
                $sTraceInfo .= "<li><a " . ($sArgsInfoDetail ? "data-toggle=\"queryphp-message-argsline-{$intKey}\" style=\"cursor: pointer;\"" : '') . "><span>#{$arrVal['line']}</span> {$arrVal['file']} - {$arrVal['class']}{$arrVal['type']}{$arrVal['function']}( {$sArgsInfo} )</a>
                " . ($sArgsInfoDetail ? "<div class=\"queryphp-message-argsline-{$intKey}\" style=\"display:none;\">
                {$sArgsInfoDetail}
                </div>" : '') . "
                </li>";
                
                unset($sArgsInfo, $sArgsInfoDetail);
            }
            $arrError['trace'] = $sTraceInfo;
            unset($sTraceInfo);
        }
        
        // 调试消息
        $arrError['message'] = $oException->getMessage();
        $arrError['type'] = $arrVal['type'] ?? '';
        $arrError['class'] = $arrTrace['0']['class'] ?? '';
        $arrError['code'] = $oException->getCode();
        $arrError['function'] = $arrTrace['0']['function'] ?? '';
        $arrError['line'] = $oException->getLine();
        $arrError['exception_type'] = get_class($oException);
        
        return $arrError;
    }
    
    /**
     * 格式化消息 API
     *
     * @param object $oException
     * @return array
     */
    protected function formatForApi($oException)
    {
        // 返回消息
        $arrError = [];
        
        // 反转一下
        $arrTrace = array_reverse($oException->getTrace());
        
        // 调试消息
        $sTraceInfo = '';
        if ($this->oProject['option']['debug']) {
            foreach ($arrTrace as $intKey => &$arrVal) {
                // 参数处理
                $arrVal['class'] = $arrVal['class'] ?? '';
                $arrVal['type'] = $arrVal['type'] ?? '';
                $arrVal['function'] = $arrVal['function'] ?? '';
                $arrVal['file'] = isset($arrVal['file']) ? Fso::tidyPathLinux($arrVal['file']) : '';
                $arrVal['line'] = $arrVal['line'] ?? '';
                $arrVal['args'] = $arrVal['args'] ?? '';
                
                // 参数格式化组装
                if (is_array($arrVal['args'])) {
                    foreach ($arrVal['args'] as $intArgsKey => $mixArgsVal) {
                        $arrVal['args'][$intArgsKey] = $this->formatArgsApi($mixArgsVal);
                    }
                }
            }
            $arrError['trace'] = $arrTrace;
        }
        
        // 调试消息
        $arrError['message'] = $oException->getMessage();
        $arrError['type'] = $arrVal['type'] ?? '';
        $arrError['class'] = $arrTrace['0']['class'] ?? '';
        $arrError['code'] = $oException->getCode();
        $arrError['function'] = $arrTrace['0']['function'] ?? '';
        $arrError['line'] = $oException->getLine();
        $arrError['exception_type'] = get_class($oException);
        $arrError['ecode'] = $arrError['code'];
        $arrError['code'] = 400;
        
        return $arrError;
    }
    
    /**
     * 格式化参数
     *
     * @param mixed $mixArgsVal
     * @return mixed
     */
    protected function formatArgs($mixArgsVal)
    {
        if (! is_string($mixArgsVal) && is_callable($mixArgsVal)) {
            if (is_array($mixArgsVal) && is_object($mixArgsVal[0])) {
                $mixArgsVal[0] = $this->formatObject($mixArgsVal[0]);
            } elseif ($mixArgsVal instanceof Closure) {
                $mixArgsVal = 'Closure';
            }
            
            return $mixArgsVal;
        } elseif (is_object($mixArgsVal)) {
            return $this->formatObject($mixArgsVal);
        } else {
            return $mixArgsVal;
        }
    }

    /**
     * 格式化 web 参数
     *
     * @param mixed $mixArgsVal
     * @return mixed
     */
    protected function formatArgsWeb($mixArgsVal)
    {
        return is_scalar($mixArgsVal) ? strip_tags(var_export($mixArgsVal, true)) : gettype($mixArgsVal);
    }

    /**
     * 格式化 api 参数
     *
     * @param mixed $mixArgsVal
     * @return mixed
     */
    protected function formatArgsApi($mixArgsVal)
    {
        return is_scalar($mixArgsVal) ? strip_tags(var_export($mixArgsVal, true)) : gettype($mixArgsVal);
    }
    
    /**
     * 格式化对象
     *
     * @param object $obj
     * @return string
     */
    protected function formatObject($obj)
    {
        $objReflectionClass = new ReflectionClass($obj);
        
        $strDes = [];
        $strDes[] = 'class ' . get_class($obj) . ' {';
        
        foreach ($objReflectionClass->getProperties() as $oProperty) {
            $arrTemp = [
                '    '
            ];
            
            if ($oProperty->isPrivate()) {
                $arrTemp[] = 'private';
            } elseif ($oProperty->isProtected()) {
                $arrTemp[] = 'protected';
            } else {
                $arrTemp[] = 'public';
            }
            
            if ($oProperty->isStatic()) {
                $arrTemp[] = 'static';
            }
            
            $arrTemp[] = '$' . $oProperty->getName() . ';';
            $strDes[] = implode(' ', $arrTemp);
        }
        
        foreach ($objReflectionClass->getMethods() as $oMethod) {
            $arrTemp = [
                '    '
            ];
            
            if ($oMethod->isPrivate()) {
                $arrTemp[] = 'private';
            } elseif ($oMethod->isProtected()) {
                $arrTemp[] = 'protected';
            } else {
                $arrTemp[] = 'public';
            }
            
            if ($oMethod->isStatic()) {
                $arrTemp[] = 'static ';
            }
            
            $arrTemp[] = 'function ' . $oMethod->getName() . '(';
            
            $arrArgTemp = [];
            $objReflection = new ReflectionMethod($obj, $oMethod->getName());
            foreach ($objReflection->getParameters() as $oArg) {
                $arrArgTemp[] = '$' . $oArg->getName();
            }
            
            $arrTemp[] = implode(', ', $arrArgTemp);
            $arrTemp[] = ');';
            $strDes[] = implode(' ', $arrTemp);
        }
        
        $strDes[] = '}';
        
        return PHP_EOL . implode(PHP_EOL, $strDes) . PHP_EOL;
    }
}
