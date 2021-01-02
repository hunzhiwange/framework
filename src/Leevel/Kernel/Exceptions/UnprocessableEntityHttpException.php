<?php

declare(strict_types=1);

namespace Leevel\Kernel\Exceptions;

use Throwable;

/**
 * 无法处理的实体.
 *
 * - 请求格式正确，但是由于含有语义错误，无法响应: 422.
 */
abstract class UnprocessableEntityHttpException extends HttpException
{
    /**
     * 构造函数.
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(422, $message, $code, $previous);
    }
}
