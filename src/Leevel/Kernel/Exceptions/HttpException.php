<?php

declare(strict_types=1);

namespace Leevel\Kernel\Exceptions;

use RuntimeException;
use Throwable;

/**
 * HTTP 异常.
 */
abstract class HttpException extends RuntimeException
{
    /**
     * HTTP 状态.
     */
    protected int $statusCode;

    /**
     * Header.
     */
    protected array $headers = [];

    /**
     * 构造函数.
     */
    public function __construct(int $statusCode, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, $code, $previous);
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
