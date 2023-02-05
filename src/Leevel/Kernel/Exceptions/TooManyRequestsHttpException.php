<?php

declare(strict_types=1);

namespace Leevel\Kernel\Exceptions;

/**
 * 请求过于频繁异常.
 *
 * - 用户在给定的时间内发送了太多的请求: 429.
 */
abstract class TooManyRequestsHttpException extends HttpException
{
    /**
     * 构造函数.
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(429, $message, $code, $previous);
    }
}
