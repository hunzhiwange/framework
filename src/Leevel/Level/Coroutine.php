<?php

declare(strict_types=1);

namespace Leevel\Level;

use Leevel\Di\ICoroutine;
use Swoole\Coroutine as SwooleCoroutine;

/**
 * 协程实现.
 */
class Coroutine implements ICoroutine
{
    /**
     * 处于协程上下文键值.
     */
    protected array $context = [];

    /**
     * {@inheritDoc}
     */
    public function inContext(string $key): bool
    {
        if (in_array($key, $this->context, true)) {
            return true;
        }

        // 将类主持到当前协程下面.
        // 通过类的静态方法 coroutineContext 返回 true 来判断.
        if (!class_exists($key)) {
            return false;
        }

        if (method_exists($key, 'coroutineContext') &&
            true === $key::coroutineContext()) {
            $this->addContext($key);

            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function addContext(...$keys): void
    {
        $this->context = array_merge($this->context, $keys);
    }

    /**
     * {@inheritDoc}
     */
    public function removeContext(...$keys): void
    {
        $this->context = array_values(array_diff($this->context, $keys));
    }

    /**
     * {@inheritDoc}
     */
    public function cid(): int
    {
        return SwooleCoroutine::getCid();
    }
}
