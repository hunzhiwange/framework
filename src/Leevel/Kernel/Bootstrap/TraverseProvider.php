<?php

declare(strict_types=1);

namespace Leevel\Kernel\Bootstrap;

use Leevel\Kernel\IApp;

/**
 * 遍历服务提供者注册服务.
 */
class TraverseProvider
{
    /**
     * 响应.
     */
    public function handle(IApp $app): void
    {
        $app->registerAppProviders();
    }
}
