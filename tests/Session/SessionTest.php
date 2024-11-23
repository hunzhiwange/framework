<?php

declare(strict_types=1);

namespace Tests\Session;

use Leevel\Cache\File as CacheFile;
use Leevel\Filesystem\Helper\DeleteDirectory;
use Leevel\Kernel\Utils\Api;
use Leevel\Session\File;
use Leevel\Session\ISession;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'Session',
    'path' => 'component/session',
    'zh-CN:description' => <<<'EOT'
QueryPHP 提供了 Session (会话) 可以用于保存用户登录状态。

内置支持的 session 驱动类型包括 file、redis，未来可能增加其他驱动。

## 使用方式

使用容器 sessions 服务

``` php
\App::make('sessions')->set(string $name, $value): void;
\App::make('sessions')->get(string $name, mixed $defaults = null);
```

依赖注入

``` php
class Demo
{
    private \Leevel\Session\Manager $session;

    public function __construct(\Leevel\Session\Manager $session)
    {
        $this->session = $session;
    }
}
```

使用代理

``` php
\Leevel\Session\Proxy\Session::proxy()->set(string $name, $value): void;
\Leevel\Session\Proxy\Session::proxy()->get(string $name, ?$value = null);
```

## session 配置

系统的 session 配置位于应用下面的 `config/session.php` 文件。

可以定义多个缓存连接，并且支持切换，每一个连接支持驱动设置。

``` php
{[file_get_contents('config/session.php')]}
```

session 参数根据不同的连接会有所区别，通用的 sesion 参数如下：

|配置项|配置描述|
|:-|:-|
|id|相当于 session_id|
|name|相当于 session_name|
|cookie_expire|COOKIE 过期时间|

::: warning 注意
QueryPHP 并没有使用 PHP 原生 SESSION，而是模拟原生 SESSION 自己实现的一套，使用方法与原生用法几乎一致。与原生 SESSION 不一样的是，QueryPHP 会在最后通过 session 中间件统一写入。
:::
EOT,
])]
final class SessionTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        $this->tearDown();
    }

    #[\Override]
    protected function tearDown(): void
    {
        $dirPath = __DIR__.'/cache';
        if (is_dir($dirPath)) {
            DeleteDirectory::handle($dirPath);
        }
    }

    #[Api([
        'zh-CN:title' => 'session 基本使用',
        'zh-CN:description' => <<<'EOT'
session 的使用方法和原生差不多。

**设置 session**

``` php
set(string $name, $value): void;
```

**是否存在 session**

``` php
has(string $name): bool;
```

**删除 session**

``` php
delete(string $name): void;
```
EOT,
    ])]
    public function testBaseUse(): void
    {
        $session = $this->createFileSessionHandler();

        $this->assertInstanceof(ISession::class, $session);
        self::assertFalse($session->isStart());
        self::assertSame('', $session->getId());
        self::assertSame('UID', $session->getName());

        $session->start();
        self::assertTrue($session->isStart());

        $session->set('hello', 'world');
        self::assertSame(['hello' => 'world'], $session->all());
        self::assertTrue($session->has('hello'));
        self::assertSame('world', $session->get('hello'));

        $session->delete('hello');
        self::assertSame([], $session->all());
        self::assertFalse($session->has('hello'));
        self::assertNull($session->get('hello'));

        $session->start();
        self::assertTrue($session->isStart());
        self::assertTrue($session->open('foo', 'bar'));
        self::assertTrue($session->close());
        self::assertTrue($session->destroy('foo'));
        self::assertSame(0, $session->gc(500));
    }

    public function testSave(): void
    {
        $session = $this->createFileSessionHandler();

        self::assertFalse($session->isStart());
        self::assertSame('', $session->getId());
        self::assertSame('UID', $session->getName());

        $session->start();
        self::assertTrue($session->isStart());

        $session->save();
        self::assertFalse($session->isStart());

        $sessionId = $session->getId();
        $dirPath = __DIR__.'/cache';
        $filePath = $dirPath.'/'.$sessionId.'.php';

        self::assertFileExists($filePath);

        $session->destroySession();
        self::assertFileDoesNotExist($filePath);
    }

    public function testSaveAndStart(): void
    {
        $session = $this->createFileSessionHandler();

        self::assertFalse($session->isStart());
        self::assertSame('', $session->getId());
        self::assertSame('UID', $session->getName());

        $session->start();
        self::assertTrue($session->isStart());
        self::assertSame([], $session->all());

        $session->set('foo', 'bar');
        $session->set('hello', 'world');
        self::assertSame(['foo' => 'bar', 'hello' => 'world'], $session->all());

        $session->save();
        self::assertFalse($session->isStart());

        $session->clear();
        self::assertSame([], $session->all());

        $session->set('other', 'value');
        self::assertSame(['other' => 'value'], $session->all());

        self::assertFalse($session->isStart());

        $sessionId = $session->getId();
        $session->start($sessionId);

        self::assertTrue($session->isStart());
        self::assertSame(['other' => 'value', 'foo' => 'bar', 'hello' => 'world', 'flash.old.key' => []], $session->all());

        $session->save();
        self::assertFalse($session->isStart());

        $sessionId = $session->getId();
        $dirPath = __DIR__.'/cache';
        $filePath = $dirPath.'/'.$sessionId.'.php';

        self::assertFileExists($filePath);

        $session->destroySession();
        self::assertFileDoesNotExist($filePath);
    }

    public function testSaveButNotStart(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Session is not start yet.');

        $session = $this->createFileSessionHandler();

        self::assertFalse($session->isStart());
        self::assertSame('', $session->getId());
        self::assertSame('UID', $session->getName());

        $session->save();
    }

    #[Api([
        'zh-CN:title' => 'setExpire 设置过期时间',
        'zh-CN:description' => <<<'EOT'
过期时间规则如下：

  * null 表示默认 session 缓存时间
  * 小与等于 0 表示永久缓存
  * 其它表示缓存多少时间，单位秒
EOT,
    ])]
    public function testSetExpire(): void
    {
        $session = $this->createFileSessionHandler();

        $session->setExpire(50);
        $session->set('hello', 'world');
        self::assertSame(['hello' => 'world'], $session->all());

        $session->start();
        $session->save();

        $sessionId = $session->getId();
        $dirPath = __DIR__.'/cache';
        $filePath = $dirPath.'/'.$sessionId.'.php';
        self::assertFileExists($filePath);
        self::assertStringContainsString('[50,', file_get_contents($filePath));
    }

    #[Api([
        'zh-CN:title' => 'put 批量插入',
    ])]
    public function testPut(): void
    {
        $session = $this->createFileSessionHandler();

        $session->put('hello', 'world');
        self::assertSame(['hello' => 'world'], $session->all());

        $session->put(['foo' => 'bar']);
        self::assertSame(['hello' => 'world', 'foo' => 'bar'], $session->all());

        $session->put(['foo' => 'bar']);
        self::assertSame(['hello' => 'world', 'foo' => 'bar'], $session->all());
    }

    #[Api([
        'zh-CN:title' => 'clear 清空 session',
    ])]
    public function testClear(): void
    {
        $session = $this->createFileSessionHandler();

        $session->set('hello', ['sub' => 'me', 'foo' => 'bar', 'hello' => 'world']);
        self::assertSame(['hello' => ['sub' => 'me', 'foo' => 'bar', 'hello' => 'world']], $session->all());

        $session->clear();
        self::assertSame([], $session->all());
    }

    #[Api([
        'zh-CN:title' => 'flash 闪存一个数据，当前请求和下一个请求可用',
    ])]
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

        self::assertSame(
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

        self::assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'flashs 批量闪存数据，当前请求和下一个请求可用',
    ])]
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

        self::assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'nowFlash 闪存一个 flash 用于当前请求使用,下一个请求将无法获取',
    ])]
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

        self::assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'nowFlashs 批量闪存数据,用于当前请求使用，下一个请求将无法获取',
    ])]
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

        self::assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'rebuildFlash 保持所有闪存数据',
    ])]
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

        self::assertSame(
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

        self::assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'keepFlash 保持闪存数据',
    ])]
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

        self::assertSame(
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

        self::assertSame(
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

        self::assertSame(
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

        self::assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'getFlash 返回闪存数据',
    ])]
    public function testGetFlash(): void
    {
        $session = $this->createFileSessionHandler();

        $session->nowFlashs(['hello' => 'world', 'foo' => 'bar']);

        self::assertSame('world', $session->getFlash('hello'));
        self::assertSame('bar', $session->getFlash('foo'));

        $session->flash('test', ['foo', 'bar']);
        self::assertSame(['foo', 'bar'], $session->getFlash('test'));
        self::assertNull($session->getFlash('notFound'));

        $session->flash('bar', ['sub' => ['foo' => 'bar']]);
        self::assertSame(['foo', 'bar'], $session->getFlash('test'));
        self::assertNull($session->getFlash('test\\notFound'));
    }

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

        self::assertSame(
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

        self::assertSame(
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

        self::assertSame(
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

        self::assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'clearFlash 清理所有闪存数据',
    ])]
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

        self::assertSame(
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

        self::assertSame(
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

        self::assertSame(
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

        self::assertSame(
            $flash,
            $this->varJson(
                $session->all()
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'setPrevUrl.prevUrl 设置和返回前一个请求地址',
    ])]
    public function testPrevUrl(): void
    {
        $session = $this->createFileSessionHandler();
        self::assertNull($session->prevUrl());
        $session->setPrevUrl('foo');
        self::assertSame('foo', $session->prevUrl());
    }

    #[Api([
        'zh-CN:title' => 'destroySession 终止会话',
    ])]
    public function testDestroy(): void
    {
        $session = $this->createFileSessionHandler();

        self::assertFalse($session->isStart());
        self::assertSame('', $session->getId());
        self::assertSame('UID', $session->getName());

        $session->start();
        self::assertTrue($session->isStart());
        self::assertNotNull($session->getId());
        self::assertNotNull($session->getName());

        $session->destroySession();
        self::assertFalse($session->isStart());
        self::assertSame('', $session->getId());
        self::assertSame('UID', $session->getName());
    }

    public function testRegenerateId(): void
    {
        $session = $this->createFileSessionHandler();

        self::assertFalse($session->isStart());
        self::assertSame('', $session->getId());
        self::assertSame('UID', $session->getName());

        $session->start();
        self::assertTrue($session->isStart());
        self::assertNotNull($sessionId = $session->getId());
        self::assertNotNull($session->getName());

        $session->regenerateId();
        self::assertFalse($sessionId === $session->getId());
    }

    protected function createFileSessionHandler(): File
    {
        return new File(new CacheFile([
            'path' => __DIR__.'/cache',
        ]));
    }
}
