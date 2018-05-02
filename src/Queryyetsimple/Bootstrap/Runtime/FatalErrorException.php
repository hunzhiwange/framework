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

use ErrorException;

/**
 * FatalErrorException
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.04.26
 * @version 1.0
 */
class FatalErrorException extends ErrorException
{

    /**
     * 原始消息
     * 
     * @var string
     */
    protected $rawMessage;

    /**
     * 构造函数
     * 
     * @param string $message
     * @param int $code
     * @param int $severity
     * @param string $filename
     * @param int $lineno
     * @param int|null $traceOffset
     * @param bool|boolean $traceArgs
     * @param array|null $trace
     * @return void
     */
    public function __construct(string $message, int $code, int $severity, string $filename, int $lineno, int $traceOffset = null, bool $traceArgs = true, array $trace = null)
    {
        $this->rawMessage = $message;

        $message = sprintf('PHP Fatal error:  %s in %s on line %d', $message, $filename ?: "eval()'d code", $lineno);

        parent::__construct($message, $code, $severity, $filename, $lineno);
    }

    /**
     * 返回原始消息
     *
     * @return string
     */
    public function getRawMessage()
    {
        return $this->rawMessage;
    }
}
