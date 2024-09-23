<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils\Assert;

class DemoClass2
{
    public function __demo2(mixed ...$hello): void {}

    public function demo1(): void {}

    protected function demo3(mixed ...$hello): void {}
}
