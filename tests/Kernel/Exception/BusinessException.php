<?php

declare(strict_types=1);

namespace Tests\Kernel\Exception;

use Leevel\Kernel\Exception\BusinessException as BaseBusinessException;

/**
 * 业务操作异常.
 *
 * - 业务异常与系统异常不同，一般不需要捕捉写入日志.
 * - 核心业务异常可以记录日志.
 */
class BusinessException extends BaseBusinessException
{
}
