<?php

declare(strict_types=1);

namespace Leevel\Di;

/**
 * 协程接口.
 */
interface ICoroutine
{
    /**
     * 是否处于协程上下文.
     */
    public function inContext(string $key): bool;

    /**
     * 添加协程上下文键值.
     */
    public function addContext(...$keys): void;

    /**
     * 删除协程上下文键值.
     */
    public function removeContext(...$keys): void;

    /**
     * 当前协程 ID.
     *
     * @see 例如 Swoole 协程 https://wiki.swoole.com/wiki/page/871.html
     */
    public function cid(): int;
}
