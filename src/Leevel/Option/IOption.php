<?php

declare(strict_types=1);

namespace Leevel\Option;

/**
 * IOption 接口.
 */
interface IOption
{
    /**
     * 默认命名空间.
    */
    public const DEFAUTL_NAMESPACE = 'app';

    /**
     * 是否存在配置.
     */
    public function has(string $name = 'app\\'): bool;

    /**
     * 获取配置.
     */
    public function get(string $name = 'app\\', mixed $defaults = null): mixed;

    /**
     * 返回所有配置.
     */
    public function all(): array;

    /**
     * 设置配置.
     */
    public function set(mixed $name, mixed  $value = null): void;

    /**
     * 删除配置.
     */
    public function delete(string $name): void;

    /**
     * 初始化配置参数.
     */
    public function reset(mixed $namespaces = null): void;
}
