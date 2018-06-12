<?php

declare(strict_types=1);

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

namespace Leevel\Kernel\Exception;

use Exception;
use RuntimeException;

/**
 * HTTP 异常.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.10
 *
 * @version 1.0
 */
class HttpException extends RuntimeException
{
    /**
     * HTTP 状态
     *
     * @var int
     */
    protected $statusCode;

    /**
     * Header.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * 构造函数.
     *
     * @param int         $statusCode
     * @param string|null $message
     * @param int         $code
     * @param \Exception  $previous
     */
    public function __construct($statusCode, $message = null, $code = 0, Exception $previous = null)
    {
        $this->statusCode = $statusCode;

        parent::__construct($message, $code, $previous);
    }

    /**
     * 设置 HTTP 状态
     *
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * 返回 HTTP 状态
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * 设置 headers.
     *
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * 返回 headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
