<?php

declare(strict_types=1);

namespace Tests\Kernel\Testing;

use Leevel\Kernel\App;
use Leevel\Kernel\Testing\TestCase as BaseTestCase;
use Tests\TestCase;

final class TestCaseTest extends TestCase
{
    public function test1(): void
    {
        $t = new TestCase1('');
        self::assertNull($this->getTestProperty($t, 'app'));
        $this->invokeTestMethod($t, 'setUp');
        $this->invokeTestMethod($t, 'setUp');
        self::assertInstanceOf(App::class, $this->getTestProperty($t, 'app'));
        $this->invokeTestMethod($t, 'tearDown');
        self::assertNull($this->getTestProperty($t, 'app'));
    }
}

class TestCase1 extends BaseTestCase
{
    protected function makeLogsDir(): array
    {
        $traceDir = '';
        $className = '';

        return [$traceDir, $className];
    }

    protected function createApp(): App
    {
        return $this->createMock(App::class);
    }
}
