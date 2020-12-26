<?php

declare(strict_types=1);

namespace Tests\Kernel\Exception;

use Leevel\Kernel\Exceptions\ForbiddenHttpException as BaseForbiddenHttpException;

/**
 * 禁止.
 *
 * - 服务器拒绝请求: 403.
 */
class ForbiddenHttpException extends BaseForbiddenHttpException
{
}
