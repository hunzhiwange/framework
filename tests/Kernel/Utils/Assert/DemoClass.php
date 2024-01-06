<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils\Assert;

class DemoClass
{
    public function demo1(): void
    {
    }

    public function demo2(string $hello, int $world): void
    {
    }

    /**
     * demo3.
     */
    public function demo3(string $hello, ?int $world = null): string
    {
        return 'hello';
    }

    public function demo4(...$hello): void
    {
    }
}
