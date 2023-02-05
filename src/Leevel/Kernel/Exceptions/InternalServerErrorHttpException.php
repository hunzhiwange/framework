<?php

declare(strict_types=1);

namespace Leevel\Kernel\Exceptions;

/**
 * 服务器内部错误.
 *
 * - 服务器遇到错误，无法完成请求: 500.
 */
abstract class InternalServerErrorHttpException extends HttpException
{
    /**
     * 构造函数.
     */
    public function __construct(string $message = '', int $code = 0, ?\Exception $previous = null)
    {
        parent::__construct(500, $message, $code, $previous);
    }
}
