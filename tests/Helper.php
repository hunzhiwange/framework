<?php

declare(strict_types=1);

namespace Tests;

/**
 * 助手方法.
 */
trait Helper
{
    /**
     * 创建日志目录.
     */
    protected function makeLogsDir(): array
    {
        $tmp = explode('\\', static::class);
        array_shift($tmp);
        $className = array_pop($tmp);
        $traceDir = dirname(__DIR__).'/logs/tests/'.implode('/', $tmp);

        if (!is_dir($traceDir)) {
            mkdir($traceDir, 0777, true);
        }

        return [$traceDir, $className];
    }
}
