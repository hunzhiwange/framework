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

namespace Leevel\Di;

/**
 * 协程接口.
 */
interface ICoroutine
{
    /**
     * 是否处于协程上下文.
     */
    public function context(string $key): bool;

    /**
     * 添加协程上下文键值.
     *
     * @param array ...$keys
     */
    public function addContext(...$keys): void;

    /**
     * 当前协程 ID.
     *
     * @see https://wiki.swoole.com/wiki/page/871.html
     */
    public function cid(): int;

    /**
     * 当前协程的父协程 ID.
     *
     * @return bool|int
     *
     * @see https://wiki.swoole.com/wiki/page/1076.html
     */
    public function pcid();
}
