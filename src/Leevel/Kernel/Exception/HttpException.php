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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Kernel\Exception;

use Exception;
use RuntimeException;

/**
 * HTTP 异常.
 */
abstract class HttpException extends RuntimeException
{
    /**
     * HTTP 状态.
     *
     * @var int
     */
    protected int $statusCode;

    /**
     * Header.
     *
     * @var array
     */
    protected array $headers = [];

    /**
     * 构造函数.
     */
    public function __construct(int $statusCode, ?string $message = null, int $code = 0, ?Exception $previous = null)
    {
        $this->statusCode = $statusCode;
        parent::__construct((string) ($message), (int) ($code), $previous);
    }

    /**
     * 设置 HTTP 状态.
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * 返回 HTTP 状态.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * 设置 headers.
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * 返回 headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * 异常是否需要上报.
     */
    public function reportable(): bool
    {
        return false;
    }
}
