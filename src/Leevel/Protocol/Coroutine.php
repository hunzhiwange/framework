<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Protocol;

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
