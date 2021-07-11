<?php

declare(strict_types=1);

namespace Leevel\Event;

use Closure;
use SplObserver;

/**
 * IDispatch 接口.
 */
interface IDispatch
{
    /**
     * 执行一个事件.
     */
    public function handle(object|string $event, ...$params): void;

    /**
     * 注册监听器.
     */
    public function register(array|object|string $event, Closure|SplObserver|string $listener, int $priority = 500): void;

    /**
     * 获取一个事件监听器.
     */
    public function get(object|string $event): array;

    /**
     * 判断事件监听器是否存在.
     */
    public function has(object|string $event): bool;

    /**
     * 删除事件所有监听器.
     */
    public function delete(object|string $event): void;
}
