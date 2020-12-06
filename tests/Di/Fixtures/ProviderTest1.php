<?php

declare(strict_types=1);

namespace Tests\Di\Fixtures;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;

class ProviderTest1 extends Provider
{
    public function __construct(IContainer $container)
    {
        $_SERVER['testMakeProvider'] = 1;
    }

    public function register(): void
    {
    }
}
