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

namespace Tests\Protocol;

use Leevel\Di\ICoroutine;
use Leevel\Protocol\Coroutine;
use Tests\TestCase;
use Throwable;

/**
 * 协程基础组件测试.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.01
 *
 * @version 1.0
 *
 * @api(
 *     title="协程基础组件",
 *     path="protocol/coroutine",
 *     description="协程基础组件主要用于返回当前协程状态以及标识一个服务是否处于协程中，以便于将数据注册到当前协程下面，用于协程上下文。",
 * )
 */
class CoroutineTest extends TestCase
{
    /**
     * @api(
     *     title="普通服务是否处于协程上下文",
     *     description="",
     *     note="",
     * )
     */
    public function testCoroutineContext(): void
    {
        $coroutine = new Coroutine();
        $this->assertInstanceOf(ICoroutine::class, $coroutine);
        $this->assertFalse($coroutine->context('notFound'));
        $coroutine->addContext('notFound');
        $this->assertTrue($coroutine->context('notFound'));
    }

    /**
     * @api(
     *     title="类是否处于协程上下文",
     *     description="类可以通过添加静态方法 `coroutineContext` 来自动完成协程上下文标识。",
     *     note="",
     * )
     */
    public function testCoroutineContextForClass(): void
    {
        $coroutine = new Coroutine();
        $this->assertFalse($coroutine->context(Demo1::class));
        $coroutine->addContext(Demo1::class);
        $this->assertTrue($coroutine->context(Demo1::class));
        $this->assertTrue($coroutine->context(Demo2::class));
    }

    /**
     * @api(
     *     title="当前协程 ID 和父 ID",
     *     description="",
     *     note="",
     * )
     */
    public function testCoroutineCidAndPcid(): void
    {
        $coroutine = new Coroutine();
        $this->assertSame(-1, $coroutine->cid());
        $this->assertFalse($coroutine->pcid());

        try {
            go(function () use ($coroutine) {
                $this->assertSame(1, $coroutine->cid());
                $this->assertSame(-1, $coroutine->pcid());

                go(function () use ($coroutine) {
                    $this->assertSame(2, $coroutine->cid());
                    $this->assertSame(1, $coroutine->pcid());
                });
            });
        } catch (Throwable $th) {
        }
    }
}

class Demo1
{
}

class Demo2
{
    public static function coroutineContext(): bool
    {
        return true;
    }
}
