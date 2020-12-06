<?php

declare(strict_types=1);

namespace Tests\Kernel;

use Exception;
use Leevel\Kernel\Inspector;
use Tests\TestCase;

class InspectorTest extends TestCase
{
    public function testBaseUse(): void
    {
        $e = new Exception('hello world');
        $inspector = new Inspector($e);
        $this->assertIsArray($this->invokeTestMethod($inspector, 'getTrace', [$e]));
    }
}
