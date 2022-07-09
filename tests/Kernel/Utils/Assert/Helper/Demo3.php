<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils\Assert\Helper;

class Demo3
{
    /**
     * demo3.
     */
    public static function handle(string $hello, ?int $world = null): string
    {
        return 'hello';
    }
}
