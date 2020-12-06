<?php

declare(strict_types=1);

namespace PHPUnit\Framework;

// 兼容执行 `php leevel make:doc` 命令时
// 无法找到 PHPUnit\Framework\TestCase 的情况
if (!class_exists('PHPUnit\\Framework\\TestCase')) {
    class TestCase
    {
    }
}

namespace Leevel\Kernel\Testing;

use Leevel\Kernel\App;
use PHPUnit\Framework\TestCase as TestCases;

/**
 * phpunit 基础测试类.
 */
abstract class TestCase extends TestCases
{
    use Helper;

    /**
     * 创建的应用.
     */
    protected ?App $app = null;

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        if (!$this->app) {
            $this->app = $this->createApp();
        }
    }

    /**
     * tearDown.
     */
    protected function tearDown(): void
    {
        $this->app = null;
    }

    /**
     * 创建日志目录.
     */
    abstract protected function makeLogsDir(): array;

    /**
     * 初始化应用.
     */
    abstract protected function createApp(): App;
}
