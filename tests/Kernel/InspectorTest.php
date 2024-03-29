<?php

declare(strict_types=1);

namespace Tests\Kernel;

use Leevel\Kernel\Inspector;
use Tests\TestCase;

final class InspectorTest extends TestCase
{
    public function testBaseUse(): void
    {
        $e = new \Exception('hello world');
        $inspector = new Inspector($e);
        static::assertIsArray($this->invokeTestMethod($inspector, 'getTrace', [$e]));
    }
}
