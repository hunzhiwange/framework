<?php

declare(strict_types=1);

namespace Tests\Di\Fixtures;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;

class ProviderTest2 extends Provider
{
    public function __construct(IContainer $container)
    {
    }

    public function bootstrap(): void
    {
        $_SERVER['testCallProviderBootstrap'] = 1;
    }

    public function register(): void
    {
    }
}
