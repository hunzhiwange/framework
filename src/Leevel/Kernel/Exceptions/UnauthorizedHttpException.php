<?php

declare(strict_types=1);

namespace Leevel\Kernel\Exceptions;

use Exception;

/**
 * 未授权.
 *
 * - 对于需要登录的网页，服务器可能返回此响应: 401.
 */
abstract class UnauthorizedHttpException extends HttpException
{
    /**
     * 构造函数.
     */
    public function __construct(?string $message = null, int $code = 0, ?Exception $previous = null)
    {
        parent::__construct(401, $message, $code, $previous);
    }
}
