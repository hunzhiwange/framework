<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils\Assert;

class DemoClass
{
    public function Demo1(): void
    {
    }

    public function Demo2(string $hello, int $world): void
    {
    }

    /**
     * demo3.
     */
    public function Demo3(string $hello, ?int $world = null): string
    {
        return 'hello';
    }

    public function Demo4(...$hello): void
    {
    }
}
