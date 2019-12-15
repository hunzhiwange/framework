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

namespace Tests\Session;

use Leevel\Session\File;
use Leevel\Session\ISession;
use Tests\TestCase;

/**
 * @api(
 *     title="session",
 *     path="component/session",
 *     description="
 * QueryPHP 提供了 Session (会话) 可以用于保存用户登录状态。
 *
 * 内置支持的 session 驱动类型包括 file、redis，未来可能增加其他驱动。
 *
 * ## 使用方式
 *
 * 使用助手函数
 *
 * ``` php
 * \Leevel\Session\Helper::get(string $name, $defaults = null);
 * \Leevel\Session\Helper::set(string $name, $value): void;
 * \Leevel\Session\Helper::session(): \Leevel\Session\Manager;
 * \Leevel\Session\Helper::getFlash(string $key, $defaults = null);
 * \Leevel\Session\Helper::flash(string $key, $value): void;
 * ```
 *
 * 使用容器 sessions 服务
 *
 * ``` php
 * \App::make('sessions')->set(string $name, $value): void;
 * \App::make('sessions')->get(string $name, $defaults = null);
 * ```
 *
 * 依赖注入
 *
 * ``` php
 * class Demo
 * {
 *     private $session;
 *
 *     public function __construct(\Leevel\Session\Manager $session)
 *     {
 *         $this->session = $session;
 *     }
 * }
 * ```
 *
 * 使用静态代理
 *
 * ``` php
 * \Leevel\Session\Proxy\Session::set(string $name, $value): void;
 * \Leevel\Session\Proxy\Session::get(string $name, $value = null);
 * ```
 *
 * ## session 配置
 *
 * 系统的 session 配置位于应用下面的 `option/session.php` 文件。
 *
 * 可以定义多个缓存连接，并且支持切换，每一个连接支持驱动设置。
 *
 * ``` php
 * {[file_get_contents('option/session.php')]}
 * ```
 *
 * session 参数根据不同的连接会有所区别，通用的 sesion 参数如下：
 *
 * |配置项|配置描述|
 * |:-|:-|
 * |id|相当于 session_id|
 * |name|相当于 session_name|
 * |expire|设置好缓存时间（小与等于 0 表示永不过期，单位时间为秒）|
 * |serialize|是否使用 serialize 编码|
 *
 * ::: warning 注意
 * QueryPHP 并没有使用 PHP 原生 SESSION，而是模拟原生 SESSION 自己实现的一套，使用方法与原生用法几乎一致。与原生 SESSION 不一样的是，QueryPHP 会在最后通过 session 中间件统一写入。
 * :::
 * ",
 * )
 */
class SessionTest extends TestCase
{
    protected function tearDown(): void
    {
        $dirPath = __DIR__.'/cache';
        if (is_dir($dirPath)) {
            rmdir($dirPath);
        }
    }

    /**
     * @api(
     *     title="session 基本使用",
     *     description="
     * session 的使用方法和原生差不多。
     *
     * **设置 session**
     *
     * ``` php
     * set(string $name, $value): void;
     * ```
     *
     * **是否存在 session**
     *
     * ``` php
     * has(string $name): bool;
     * ```
     *
     * **删除 session**
     *
     * ``` php
     * delete(string $name): void;
     * ```
     * ",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $session = $this->createFileSessionHandler();

        $this->assertInstanceof(ISession::class, $session);
        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertSame('UID', $session->getName());

        $session->start();
        $this->assertTrue($session->isStart());

        $session->set('hello', 'world');
        $this->assertSame(['hello' => 'world'], $session->all());
        $this->assertTrue($session->has('hello'));
        $this->assertSame('world', $session->get('hello'));

        $session->delete('hello');
        $this->assertSame([], $session->all());
        $this->assertFalse($session->has('hello'));
        $this->assertNull($session->get('hello'));

        $session->start();
        $this->assertTrue($session->isStart());
        $this->assertTrue($session->open('foo', 'bar'));
        $this->assertTrue($session->close());
        $this->assertTrue($session->destroy('foo'));
        $this->assertSame(0, $session->gc(500));
    }

    public function testSave(): void
    {
        $session = $this->createFileSessionHandler();

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertSame('UID', $session->getName());

        $session->start();
        $this->assertTrue($session->isStart());

        $session->save();
        $this->assertFalse($session->isStart());

        $sessionId = $session->getId();
        $dirPath = __DIR__.'/cache';
        $filePath = $dirPath.'/'.$sessionId.'.php';

        $this->assertFileExists($filePath);

        $session->destroySession();
        $this->assertFileNotExists($filePath);
    }

    public function testSaveAndStart(): void
    {
        $session = $this->createFileSessionHandler();

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertSame('UID', $session->getName());

        $session->start();
        $this->assertTrue($session->isStart());
        $this->assertSame([], $session->all());

        $session->set('foo', 'bar');
        $session->set('hello', 'world');
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $session->all());

        $session->save();
        $this->assertFalse($session->isStart());

        $session->clear();
        $this->assertSame([], $session->all());

        $session->set('other', 'value');
        $this->assertSame(['other' => 'value'], $session->all());

        $this->assertFalse($session->isStart());

        $sessionId = $session->getId();
        $session->start($sessionId);

        $this->assertTrue($session->isStart());
        $this->assertSame(['other' => 'value', 'foo' => 'bar', 'hello' => 'world', 'flash.old.key' => []], $session->all());

        $session->save();
        $this->assertFalse($session->isStart());

        $sessionId = $session->getId();
        $dirPath = __DIR__.'/cache';
        $filePath = $dirPath.'/'.$sessionId.'.php';

        $this->assertFileExists($filePath);

        $session->destroySession();
        $this->assertFileNotExists($filePath);
    }

    public function testSaveButNotStart(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Session is not start yet.');

        $session = $this->createFileSessionHandler();

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertSame('UID', $session->getName());

        $session->save();
    }

    /**
     * @api(
     *     title="put 批量插入",
     *     description="",
     *     note="",
     * )
     */
    public function testPut(): void
    {
        $session = $this->createFileSessionHandler();

        $session->put('hello', 'world');
        $this->assertSame(['hello' => 'world'], $session->all());

        $session->put(['foo' => 'bar']);
        $this->assertSame(['hello' => 'world', 'foo' => 'bar'], $session->all());

        $session->put(['foo' => 'bar']);
        $this->assertSame(['hello' => 'world', 'foo' => 'bar'], $session->all());
    }

    /**
     * @api(
     *     title="push 数组插入数据",
     *     description="",
     *     note="",
     * )
     */
    public function testPush(): void
    {
        $session = $this->createFileSessionHandler();

        $session->push('hello', 'world');
        $this->assertSame(['hello' => ['world']], $session->all());

        $session->push('hello', 'bar');
        $this->assertSame(['hello' => ['world', 'bar']], $session->all());

        $session->push('hello', 'bar');
        $this->assertSame(['hello' => ['world', 'bar', 'bar']], $session->all());
    }

    /**
     * @api(
     *     title="merge 合并元素",
     *     description="",
     *     note="",
     * )
     */
    public function testMerge(): void
    {
        $session = $this->createFileSessionHandler();

        $session->merge('hello', ['world']);
        $this->assertSame(['hello' => ['world']], $session->all());

        $session->merge('hello', ['bar']);
        $this->assertSame(['hello' => ['world', 'bar']], $session->all());

        $session->merge('hello', ['bar']);
        $this->assertSame(['hello' => ['world', 'bar', 'bar']], $session->all());

        $session->merge('hello', ['he' => 'he']);
        $this->assertSame(['hello' => ['world', 'bar', 'bar', 'he' => 'he']], $session->all());
    }

    /**
     * @api(
     *     title="pop 弹出元素",
     *     description="",
     *     note="",
     * )
     */
    public function testPop(): void
    {
        $session = $this->createFileSessionHandler();

        $session->set('hello', ['foo', 'bar', 'world', 'sub' => 'me']);
        $this->assertSame(['hello' => ['foo', 'bar', 'world', 'sub' => 'me']], $session->all());

        $session->pop('hello', ['bar']);
        $this->assertSame(['hello' => ['foo', 2 => 'world', 'sub' => 'me']], $session->all());

        $session->pop('hello', ['me']);
        $this->assertSame(['hello' => ['foo', 2 => 'world']], $session->all());

        $session->pop('hello', ['foo', 'world']);
        $this->assertSame(['hello' => []], $session->all());
    }

    /**
     * @api(
     *     title="arr 数组插入键值对数据",
     *     description="",
     *     note="",
     * )
     */
    public function testArr(): void
    {
        $session = $this->createFileSessionHandler();

        $session->arr('hello', ['sub' => 'me']);
        $this->assertSame(['hello' => ['sub' => 'me']], $session->all());

        $session->arr('hello', 'foo', 'bar');
        $this->assertSame(['hello' => ['sub' => 'me', 'foo' => 'bar']], $session->all());

        $session->arr('hello', 'foo', 'bar');
        $this->assertSame(['hello' => ['sub' => 'me', 'foo' => 'bar']], $session->all());
    }

    /**
     * @api(
     *     title="arrDelete 数组键值删除数据",
     *     description="",
     *     note="",
     * )
     */
    public function testArrDelete(): void
    {
        $session = $this->createFileSessionHandler();

        $session->set('hello', ['sub' => 'me', 'foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['hello' => ['sub' => 'me', 'foo' => 'bar', 'hello' => 'world']], $session->all());

        $session->arrDelete('hello', ['sub', 'foo']);
        $this->assertSame(['hello' => ['hello' => 'world']], $session->all());

        $session->arrDelete('hello', 'foo');
        $this->assertSame(['hello' => ['hello' => 'world']], $session->all());

        $session->arrDelete('hello', 'hello');
        $this->assertSame(['hello' => []], $session->all());
    }

    /**
     * @api(
     *     title="getPart 返回数组部分数据",
     *     description="",
     *     note="",
     * )
     */
    public function testGetPart(): void
    {
        $session = $this->createFileSessionHandler();

        $session->set('hello', ['sub' => 'me', 'foo' => 'bar', 'hello' => 'world', 'sub2' => ['foo' => ['foo' => 'bar']]]);
        $this->assertSame(['hello' => ['sub' => 'me', 'foo' => 'bar', 'hello' => 'world', 'sub2' => ['foo' => ['foo' => 'bar']]]], $session->all());

        $this->assertSame('me', $session->getPart('hello\\sub'));
        $this->assertSame(['foo' => 'bar'], $session->getPart('hello\\sub2.foo'));
        $this->assertNull($session->getPart('hello\\sub2.foo.notFound'));
        $this->assertNull($session->getPart('hello\\notFound'));
        $this->assertSame(123, $session->getPart('hello\\notFound', 123));
    }

    public function testGetPart2(): void
    {
        $session = $this->createFileSessionHandler();

        $session->set('hello', 'bar');

        $this->assertNull($session->getPart('hello\\sub'));
    }

    /**
     * @api(
     *     title="clear 清空 session",
     *     description="",
     *     note="",
     * )
     */
    public function testClear(): void
    {
        $session = $this->createFileSessionHandler();

        $session->set('hello', ['sub' => 'me', 'foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['hello' => ['sub' => 'me', 'foo' => 'bar', 'hello' => 'world']], $session->all());

        $session->clear();
        $this->assertSame([], $session->all());
    }

    /**
     * @api(
     *     title="flash 闪存一个数据，当前请求和下一个请求可用",
     *     description="",
     *     note="",
     * )
     */
    public function testFlash(): void
    {
        $session = $this->createFileSessionHandler();

        $session->flash('hello', 'world');

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.new.key": [
                    "hello"
                ],
                "flash.old.key": []
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );

        $session->flash('foo', ['bar']);

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.new.key": [
                    "hello",
                    "foo"
                ],
                "flash.old.key": [],
                "flash.data.foo": [
                    "bar"
                ]
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    /**
     * @api(
     *     title="flashs 批量闪存数据，当前请求和下一个请求可用",
     *     description="",
     *     note="",
     * )
     */
    public function testFlashs(): void
    {
        $session = $this->createFileSessionHandler();

        $session->flashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.new.key": [
                    "hello",
                    "foo"
                ],
                "flash.old.key": [],
                "flash.data.foo": "bar"
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    /**
     * @api(
     *     title="nowFlash 闪存一个 flash 用于当前请求使用,下一个请求将无法获取",
     *     description="",
     *     note="",
     * )
     */
    public function testNowFlash(): void
    {
        $session = $this->createFileSessionHandler();

        $session->nowFlash('hello', 'world');

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.old.key": [
                    "hello"
                ]
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    /**
     * @api(
     *     title="nowFlashs 批量闪存数据,用于当前请求使用，下一个请求将无法获取",
     *     description="",
     *     note="",
     * )
     */
    public function testNowFlashs(): void
    {
        $session = $this->createFileSessionHandler();

        $session->nowFlashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.old.key": [
                    "hello",
                    "foo"
                ],
                "flash.data.foo": "bar"
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    /**
     * @api(
     *     title="rebuildFlash 保持所有闪存数据",
     *     description="",
     *     note="",
     * )
     */
    public function testRebuildFlash(): void
    {
        $session = $this->createFileSessionHandler();

        $session->nowFlashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.old.key": [
                    "hello",
                    "foo"
                ],
                "flash.data.foo": "bar"
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );

        $session->rebuildFlash();

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.old.key": [],
                "flash.data.foo": "bar",
                "flash.new.key": [
                    "hello",
                    "foo"
                ]
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    /**
     * @api(
     *     title="keepFlash 保持闪存数据",
     *     description="",
     *     note="",
     * )
     */
    public function testKeepFlash(): void
    {
        $session = $this->createFileSessionHandler();

        $session->nowFlashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.old.key": [
                    "hello",
                    "foo"
                ],
                "flash.data.foo": "bar"
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );

        $session->keepFlash(['hello', 'foo']);

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.old.key": [],
                "flash.data.foo": "bar",
                "flash.new.key": [
                    "hello",
                    "foo"
                ]
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    public function testKeepFlash2(): void
    {
        $session = $this->createFileSessionHandler();

        $session->nowFlashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.old.key": [
                    "hello",
                    "foo"
                ],
                "flash.data.foo": "bar"
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );

        $session->keepFlash(['hello', 'foo']);

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.old.key": [],
                "flash.data.foo": "bar",
                "flash.new.key": [
                    "hello",
                    "foo"
                ]
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    /**
     * @api(
     *     title="getFlash 返回闪存数据",
     *     description="",
     *     note="",
     * )
     */
    public function testGetFlash(): void
    {
        $session = $this->createFileSessionHandler();

        $session->nowFlashs(['hello' => 'world', 'foo' => 'bar']);

        $this->assertSame('world', $session->getFlash('hello'));
        $this->assertSame('bar', $session->getFlash('foo'));

        $session->flash('test', ['foo', 'bar']);
        $this->assertSame(['foo', 'bar'], $session->getFlash('test'));
        $this->assertSame('foo', $session->getFlash('test\\0'));
        $this->assertNull($session->getFlash('notFound'));

        $session->flash('bar', ['sub' => ['foo' => 'bar']]);
        $this->assertSame(['foo', 'bar'], $session->getFlash('test'));
        $this->assertNull($session->getFlash('test\\notFound'));
    }

    /**
     * @api(
     *     title="deleteFlash 删除闪存数据",
     *     description="",
     *     note="",
     * )
     */
    public function deleteFlash(): void
    {
        $session = $this->createFileSessionHandler();

        $session->flashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.new.key": [
                    "hello",
                    "foo"
                ],
                "flash.old.key": [],
                "flash.data.foo": "bar"
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );

        $session->deleteFlash(['hello', 'foo']);

        $flash = <<<'eot'
            {
                "flash.new.key": [],
                "flash.old.key": [
                    "hello",
                    "foo"
                ]
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    public function testDeleteFlash2(): void
    {
        $session = $this->createFileSessionHandler();

        $session->flashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.new.key": [
                    "hello",
                    "foo"
                ],
                "flash.old.key": [],
                "flash.data.foo": "bar"
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );

        $session->deleteFlash(['hello']);

        $flash = <<<'eot'
            {
                "flash.new.key": {
                    "1": "foo"
                },
                "flash.old.key": [
                    "hello"
                ],
                "flash.data.foo": "bar"
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    /**
     * @api(
     *     title="clearFlash 清理所有闪存数据",
     *     description="",
     *     note="",
     * )
     */
    public function testClearFlash(): void
    {
        $session = $this->createFileSessionHandler();

        $session->flashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.new.key": [
                    "hello",
                    "foo"
                ],
                "flash.old.key": [],
                "flash.data.foo": "bar"
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );

        $session->clearFlash();

        $flash = <<<'eot'
            {
                "flash.new.key": [],
                "flash.old.key": [
                    "hello",
                    "foo"
                ]
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    public function testUnregisterFlash(): void
    {
        $session = $this->createFileSessionHandler();

        $session->nowFlashs(['hello' => 'world', 'foo' => 'bar']);
        $session->flashs(['hello2' => 'world', 'foo2' => 'bar']);

        $flash = <<<'eot'
            {
                "flash.data.hello": "world",
                "flash.old.key": [
                    "hello",
                    "foo"
                ],
                "flash.data.foo": "bar",
                "flash.data.hello2": "world",
                "flash.new.key": [
                    "hello2",
                    "foo2"
                ],
                "flash.data.foo2": "bar"
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );

        $session->unregisterFlash();

        $flash = <<<'eot'
            {
                "flash.old.key": [
                    "hello2",
                    "foo2"
                ],
                "flash.data.hello2": "world",
                "flash.data.foo2": "bar"
            }
            eot;

        $this->assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    /**
     * @api(
     *     title="setPrevUrl.prevUrl 设置和返回前一个请求地址",
     *     description="",
     *     note="",
     * )
     */
    public function testPrevUrl(): void
    {
        $session = $this->createFileSessionHandler();
        $this->assertNull($session->prevUrl());
        $session->setPrevUrl('foo');
        $this->assertSame('foo', $session->prevUrl());
    }

    /**
     * @api(
     *     title="destroySession 终止会话",
     *     description="",
     *     note="",
     * )
     */
    public function testDestroy(): void
    {
        $session = $this->createFileSessionHandler();

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertSame('UID', $session->getName());

        $session->start();
        $this->assertTrue($session->isStart());
        $this->assertNotNull($session->getId());
        $this->assertNotNull($session->getName());

        $session->destroySession();
        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertSame('UID', $session->getName());
    }

    public function testRegenerateId(): void
    {
        $session = $this->createFileSessionHandler();

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertSame('UID', $session->getName());

        $session->start();
        $this->assertTrue($session->isStart());
        $this->assertNotNull($sessionId = $session->getId());
        $this->assertNotNull($session->getName());

        $session->regenerateId();
        $this->assertFalse($sessionId === $session->getId());
    }

    protected function createFileSessionHandler(): File
    {
        return new File([
            'path' => __DIR__.'/cache',
        ]);
    }
}
