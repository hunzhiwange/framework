<?php

declare(strict_types=1);

namespace Leevel\Cache;

/**
 * 缓存块接口.
 */
interface IBlock
{
    /**
     * 响应.
     */
    public function handle(array $params = []): mixed;

    /**
     * 缓存驱动.
     */
    public function cache(): ICache;

    /**
     * 缓存 key.
     */
    public static function key(array $params = []): string;
}
