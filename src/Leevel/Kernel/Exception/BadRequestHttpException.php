<?php

declare(strict_types=1);

namespace Leevel\Kernel\Exception;

use Exception;

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
    public function __construct(?string $message = null, int $code = 0, ?Exception $previous = null)
    {
        parent::__construct(400, $message, $code, $previous);
    }
}
