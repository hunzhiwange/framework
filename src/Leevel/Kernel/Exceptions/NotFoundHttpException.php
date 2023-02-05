<?php

declare(strict_types=1);

namespace Leevel\Kernel\Exceptions;

/**
 * 未找到.
 *
 * - 用户发出的请求针对的是不存在的记录: 404.
 */
abstract class NotFoundHttpException extends HttpException
{
    /**
     * 构造函数.
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(404, $message, $code, $previous);
    }
}
