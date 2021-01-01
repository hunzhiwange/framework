<?php

declare(strict_types=1);

namespace Leevel\Kernel\Exceptions;

use Throwable;

/**
 * 方法禁用.
 *
 * - 禁用请求中指定的方法: 405.
 */
abstract class MethodNotAllowedHttpException extends HttpException
{
    /**
     * 构造函数.
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(405, $message, $code, $previous);
    }
}
