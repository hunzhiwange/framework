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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Protocol;

use Leevel\Di\ICoroutine;
use Swoole\Coroutine as SwooleCoroutine;

/**
 * 协程实现.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.12.14
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Coroutine implements ICoroutine
{
    /**
     * 是否处于协程上下文.
     *
     * @param string $className
     *
     * @return bool
     */
    public function context(string $className): bool
    {
        if (!class_exists($className)) {
            return false;
        }

        if (method_exists($className, 'coroutineContext') &&
            true === $className::coroutineContext()) {
            return true;
        }

        return false;
    }

    /**
     * 当前协程 ID.
     *
     * @return int
     */
    public function uid(): int
    {
        return SwooleCoroutine::getuid();
    }
}
