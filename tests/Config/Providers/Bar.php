<?php

declare(strict_types=1);

namespace Tests\Config\Providers;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;

/**
 * bar 服务提供者.
 */
class Bar extends Provider
{
    public function register(): void
    {
        $this->container->singleton('bar', function (IContainer $container) {
            return 'foo';
        });
    }

    public static function providers(): array
    {
        return [
            'bar' => [
                'Tests\\Config\\Providers\\World',
            ],
            'helloworld',
        ];
    }

    public static function isDeferred(): bool
    {
        return true;
    }
}
