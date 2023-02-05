<?php

declare(strict_types=1);

namespace Leevel\Kernel\Exceptions;

/**
 * 禁止.
 *
 * - 服务器拒绝请求: 403.
 */
abstract class ForbiddenHttpException extends HttpException
{
    /**
     * 构造函数.
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(403, $message, $code, $previous);
    }
}
