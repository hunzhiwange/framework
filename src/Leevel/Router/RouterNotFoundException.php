<?php

declare(strict_types=1);

namespace Leevel\Router;

/**
 * 路由未找到异常.
 */
class RouterNotFoundException extends \RuntimeException
{
    /**
     * 异常是否需要上报.
     */
    public function reportable(): bool
    {
        return false;
    }
}
