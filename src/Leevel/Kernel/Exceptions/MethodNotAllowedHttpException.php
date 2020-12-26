<?php

declare(strict_types=1);

namespace Leevel\Kernel\Exceptions;

use Exception;

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
    public function __construct(?string $message = null, int $code = 0, ?Exception $previous = null)
    {
        parent::__construct(405, $message, $code, $previous);
    }
}
