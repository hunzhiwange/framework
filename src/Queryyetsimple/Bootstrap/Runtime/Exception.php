<?php
/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 * 
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Bootstrap\Runtime;

use Closure;
use Throwable;
use Whoops\Run;
use ReflectionClass;
use ReflectionMethod;
use Exception as Exceptions;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use Queryyetsimple\{
    Http\Response,
    Di\IContainer,
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
    protected $exception;
    
    /**
     * 构造函数
     *
     * @param \Queryyetsimple\Bootstrap\Project $project
     * @param object $exception
     * @return void
     */
    public function __construct($project, $exception)
    {
        $this->project = $project;
        $this->exception = $exception;
        $this->strMessage = "[{$this->exception->getCode()}] {$this->exception->getMessage()} " . 
            basename($this->exception->getFile()) . 
            sprintf(" line %d", $this->exception->getLine());
    }
    
    /**
     * 错误消息执行入口
     *
     * @return void
     */
    public function run()
    {
        $this->log($this->strMessage);

        if ($this->project['option']['debug_driver'] == 'whoops' && $this->project['option']->get('debug')) {
            return $this->renderExceptionWithWhoops($this->exception);
        } else {
            $this->toResponse($this->project['option']['default_response'] == 'api' ? 
                $this->formatForApi($this->exception) : 
                $this->format($this->exception));
        }
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
            $mixError = (array) $mixError;
        }

        if ($this->project['option']['default_response'] == 'api') {
            return $mixError;
        }
        
        if (! $this->project['option']->get('debug')) {
            $mixError['message'] = $this->project['option']->get('custom_exception_message');
        }
        
        // 包含异常页面模板
        if (PHP_SAPI == 'cli') {
            echo $mixError['message'];
        } else {
            $exceptionpath = $this->project['option']->get('custom_exception_template') ?:
                $this->project->pathSystem('exception');
            
            if (! is_file($exceptionpath)) {
                exit(sprintf('Exception file %s is not exits.', $exceptionpath));
            }

            require_once $exceptionpath;
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
        if ($this->project['option']['debug']) {
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
                $sTraceInfo .= "<li><a " . 
                    ($sArgsInfoDetail ? "data-toggle=\"queryphp-message-argsline-{$intKey}\" style=\"cursor: pointer;\"" : '') . 
                    "><span>#{$arrVal['line']}</span> {$arrVal['file']} - {$arrVal['class']}{$arrVal['type']}{$arrVal['function']}( {$sArgsInfo} )</a>" . 
                    ($sArgsInfoDetail ? 
                        "<div class=\"queryphp-message-argsline-{$intKey}\" style=\"display:none;\">{$sArgsInfoDetail}</div>" 
                        : ''
                    ) . 
                    "</li>";
                
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
        if ($this->project['option']['debug']) {
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

    /**
     * Whoops 接管异常
     * 
     * @param \Throwable $e
     * @return \Queryyetsimple\Http\Response
     */
    protected function renderExceptionWithWhoops(Throwable $e)
    {
        $whoops = new Run;

        if ($this->project['option']['default_response'] == 'api') {
            $whoops->pushHandler(new JsonResponseHandler);
        } else {
            $whoops->pushHandler(new PrettyPageHandler);
        }

        return new Response(
            $whoops->handleException($e),
            $e->getStatusCode(),
            $e->getHeaders()
        );
    }
}
