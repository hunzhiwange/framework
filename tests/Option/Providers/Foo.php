<?php

declare(strict_types=1);

namespace Tests\Option\Providers;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;

/**
 * foo 服务提供者.
 */
class Foo extends Provider
{
    public function register(): void
    {
        $this->container->singleton('foo', function (IContainer $container) {
            return 'bar';
        });
    }

    public static function providers(): array
    {
        return [
            'foo' => [
                'Tests\\Option\\Providers\\Hello',
            ],
        ];
    }
}
