<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils\Assert;

class DemoClass4
{
    public function demo1(bool $isBool = false): bool|string
    {
        if ($isBool) {
            return false;
        }

        return 'foo';
    }

    public function demo2(bool|string $isBool): bool|string
    {
        if ($isBool) {
            return false;
        }

        return 'foo';
    }

    public function demo3(DemoClass3 $demoClass3): string
    {
        return $demoClass3::class;
    }

    public function demo4(?DemoClass3 $demoClass3 = null): string
    {
        return $demoClass3 ? $demoClass3::class : 'not_found';
    }
}
