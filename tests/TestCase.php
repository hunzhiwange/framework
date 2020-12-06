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

namespace Tests;

use Leevel\Kernel\Testing\Helper as BaseHelper;
use PHPUnit\Framework\TestCase as TestCases;

/**
 * phpunit 基础测试类.
 */
abstract class TestCase extends TestCases
{
    use BaseHelper;
    use Helper;
}
