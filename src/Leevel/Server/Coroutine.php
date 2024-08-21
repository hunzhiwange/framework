<?php

declare(strict_types=1);

namespace Leevel\Server;

use Leevel\Di\Container;
use Swoole\Coroutine as SwooleCoroutine;

/**
 * 协程管理.
 */
class Coroutine extends SwooleCoroutine
{
    public static function create(callable $func, ...$param): false|int
    {
        $newFunc = static function () use ($func): void {
            Container::parentContext();
            $func();
        };

        return parent::create($newFunc, ...$param);
    }

    /**
     * 设置父级协程上下文解析到子协程回调.
     */
    public static function parentContextResolver(): void
    {
        $parentCoroutineId = static::getPcid();
        if (false === $parentCoroutineId) {
            return;
        }

        $parentContext = static::getContext($parentCoroutineId);
        if (!isset($parentContext)) {
            return;
        }

        $currentContext = static::getContext();
        if (!isset($currentContext)) {
            return;
        }

        foreach ($parentContext as $key => $value) {
            $currentContext[$key] = $value;
        }
    }
}
