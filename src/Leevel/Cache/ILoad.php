<?php

declare(strict_types=1);

namespace Leevel\Cache;

/**
 * Cache 快捷载入接口.
 */
interface ILoad
{
    /**
     * 载入缓存数据.
     *
     * - 系统自动存储缓存到内存，可重复执行不会重复载入数据.
     */
    public function data(array $names, ?int $expire = null, bool $force = false): mixed;

    /**
     * 刷新缓存数据.
     */
    public function refresh(array $names): void;

    /**
     * 清理已载入的缓存数据.
     */
    public function clearCacheLoaded(?array $names = null): void;
}
