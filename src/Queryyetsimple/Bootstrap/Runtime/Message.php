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
namespace Leevel\Bootstrap\Runtime;

use Leevel\Log\Ilog;

/**
 * 消息基类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.04
 * @version 1.0
 */
abstract class Message
{

    /**
     * 返回项目容器
     *
     * @var \Leevel\Bootstrap\Project
     */
    protected $project;

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
    public function run()
    {
        if ($this->strMessage) {
            $this->log($this->strMessage);
            $this->toResponse($this->strMessage);
        }
    }

    /**
     * 记录日志
     *
     * @param string $strMessage
     * @return void
     */
    protected function log($strMessage)
    {
        if ($this->project['option']->get('log\runtime_enabled', false)) {
            $this->project['log']->write(ILog::ERROR, $strMessage);
        }
    }

    /**
     * 输出一个致命错误
     *
     * @param string $sMessage
     * @return void
     */
    protected function errorMessage($sMessage)
    {
        $errorpath = $this->project->pathSystem('error');

        if (! is_file($errorpath)) {
            exit(sprintf('Error file %s is not exits.', $errorpath));
        }

        require_once $errorpath;
    }

    /**
     * 格式为 response
     *
     * @param string $sMessage
     * @return void
     */
    protected function toResponse($sMessage)
    {
        if (property_exists($this, 'objException') && method_exists($this->objException, 'getResponse')) {
            return $this->objException->getResponse()->output();
        }

        if ($this->project['option']['default_response'] == 'api') {
            $strContent = $this->errorMessage($sMessage);
        } else {
            $intLevel = ob_get_level();
            ob_start();

            try {
                $this->errorMessage($sMessage);
            } catch (Exceptions $oE) {
                while (ob_get_level() > $intLevel) {
                    ob_end_clean();
                }

                throw $oE;
            }

            $strContent = ob_get_clean();
        }

        $statusCode = property_exists($this, 'objException') && method_exists($this->objException, 'statusCode') ?
            $this->objException->statusCode() : 
            404;

        $response = $this->project['response']->make($strContent);

        $response->setStatusCode($statusCode)->

        send();
    }
}
