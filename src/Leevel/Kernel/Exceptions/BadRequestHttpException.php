<?php

declare(strict_types=1);

namespace Leevel\Kernel\Exceptions;

/**
 * 错误请求.
 *
 * - 服务器不理解请求的语法: 400.
 */
abstract class BadRequestHttpException extends HttpException
{
    /**
     * 构造函数.
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(400, $message, $code, $previous);
    }
}
