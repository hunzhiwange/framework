<?php

declare(strict_types=1);

namespace Tests\Level;

use Leevel\Di\ICoroutine;
use Leevel\Level\Coroutine;
use Tests\TestCase;
use Throwable;

/**
 * @api(
 *     zh-CN:title="协程基础组件",
 *     path="level/coroutine",
 *     zh-CN:description="协程基础组件主要用于返回当前协程状态以及标识一个服务是否处于协程中，以便于将数据注册到当前协程下面，用于协程上下文。",
 * )
 */
class CoroutineTest extends TestCase
{
    protected function setUp(): void
    {
        if (!extension_loaded('swoole')) {
            $this->markTestSkipped('Swoole extension must be loaded before use.');
        }
    }

    /**
     * @api(
     *     zh-CN:title="inContext 普通服务是否处于协程上下文",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testCoroutineContext(): void
    {
        $coroutine = new Coroutine();
        $this->assertInstanceOf(ICoroutine::class, $coroutine);
        $this->assertFalse($coroutine->inContext('notFound'));
        $coroutine->addContext('notFound');
        $this->assertTrue($coroutine->inContext('notFound'));
    }

    /**
     * @api(
     *     zh-CN:title="inContext 类是否处于协程上下文",
     *     zh-CN:description="类可以通过添加静态方法 `coroutineContext` 来自动完成协程上下文标识。",
     *     zh-CN:note="",
     * )
     */
    public function testCoroutineContextForClass(): void
    {
        $coroutine = new Coroutine();
        $this->assertFalse($coroutine->inContext(Demo1::class));
        $coroutine->addContext(Demo1::class);
        $this->assertTrue($coroutine->inContext(Demo1::class));
        $this->assertTrue($coroutine->inContext(Demo2::class));
    }

    /**
     * @api(
     *     zh-CN:title="addContext 添加协程上下文键值",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testAddContext(): void
    {
        $coroutine = new Coroutine();
        $this->assertFalse($coroutine->inContext('hello'));
        $this->assertFalse($coroutine->inContext(Demo1::class));
        $coroutine->addContext(Demo1::class, 'hello');
        $this->assertTrue($coroutine->inContext('hello'));
        $this->assertTrue($coroutine->inContext(Demo1::class));
    }

    /**
     * @api(
     *     zh-CN:title="removeContext 删除协程上下文键值",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testRemoveContext(): void
    {
        $coroutine = new Coroutine();
        $this->assertFalse($coroutine->inContext('hello'));
        $this->assertFalse($coroutine->inContext(Demo1::class));
        $coroutine->addContext(Demo1::class, 'hello');
        $this->assertTrue($coroutine->inContext('hello'));
        $this->assertTrue($coroutine->inContext(Demo1::class));
        $coroutine->removeContext(Demo1::class, 'hello');
        $this->assertFalse($coroutine->inContext('hello'));
        $this->assertFalse($coroutine->inContext(Demo1::class));
    }

    /**
     * @api(
     *     zh-CN:title="当前协程 ID",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testCoroutineCid(): void
    {
        $coroutine = new Coroutine();
        $this->assertSame(-1, $coroutine->cid());

        try {
            go(function () use ($coroutine) {
                $this->assertSame(1, $coroutine->cid());

                go(function () use ($coroutine) {
                    $this->assertSame(2, $coroutine->cid());
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
