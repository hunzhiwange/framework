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

/**
 * 错误消息
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.04
 * @version 1.0
 */
class Error extends Message
{
    
    /**
     * 构造函数
     *
     * @param \Leevel\Bootstrap\Project $project
     * @param int $errno
     * @param string $sErrStr
     * @param string $sErrFile
     * @param int $nErrLine
     * @return void
     */
    public function __construct($project, $errno, $sErrStr, $sErrFile, $nErrLine)
    {
        // 根据 error_reporing 等级来确定是否抛出错误
        if (! (error_reporting() & $errno)) {
            return false;
        }

        $this->project = $project;
        if ($errno) {
            $this->strMessage = "[{$errno}]: {$sErrStr}<br> File: {$sErrFile}<br> Line: {$nErrLine}";
        }
    }
}
