<?php

declare(strict_types=1);

namespace Tests\Kernel;

use App as Apps;
use Leevel;
use Leevel\Di\Container;
use Leevel\I18n\II18n;
use Leevel\Kernel\App;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '内核助手函数',
    'path' => 'architecture/kernel/functions',
    'zh-CN:description' => <<<'EOT'
QueryPHP 在内核助手函数中为代理应用 `\Leevel\Kernel\Proxy\App` 提供了两个别名类 `\App` 和 `\Leevel`，提供简洁的静态访问入口。

例外还提供了一个语言包函数 `__`，为应用提供国际化支持。
EOT,
])]
final class FunctionsTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    #[Api([
        'zh-CN:title' => 'Leevel 应用静态代理别名类调用应用',
    ])]
    public function testLeevel(): void
    {
        $this->createContainer();
        static::assertSame(App::VERSION, \Leevel::version());
    }

    #[Api([
        'zh-CN:title' => 'App 应用静态代理别名类调用应用',
    ])]
    public function testApp(): void
    {
        $this->createContainer();
        static::assertSame(App::VERSION, Apps::version());
    }

    #[Api([
        'zh-CN:title' => 'Leevel 应用静态代理别名类调用 IOC 容器',
    ])]
    public function testLeevelWithContainerMethod(): void
    {
        $this->createContainer();
        static::assertNull(\Leevel::make('foo', throw: false));
    }

    #[Api([
        'zh-CN:title' => 'App 应用静态代理别名类调用 IOC 容器',
    ])]
    public function testAppWithContainerMethod(): void
    {
        $this->createContainer();
        static::assertNull(Apps::make('foo', throw: false));
    }

    #[Api([
        'zh-CN:title' => '全局语言函数 __()',
    ])]
    public function testFunctionLang(): void
    {
        $container = $this->createContainer();
        static::assertNull(Apps::make('foo', throw: false));

        $i18n = $this->createMock(II18n::class);
        $map = [
            ['hello', 'hello'],
            ['hello %s', 'foo', 'hello foo'],
            ['hello %d', 5, 'hello 5'],
        ];
        $i18n->method('gettext')->willReturnMap($map);
        static::assertSame('hello', $i18n->gettext('hello'));
        static::assertSame('hello foo', $i18n->gettext('hello %s', 'foo'));
        static::assertSame('hello 5', $i18n->gettext('hello %d', 5));

        $container = $this->createContainer();
        $container->singleton('i18n', function () use ($i18n) {
            return $i18n;
        });

        static::assertSame('hello', __('hello'));
        static::assertSame('hello foo', __('hello %s', 'foo'));
        static::assertSame('hello 5', __('hello %d', 5));
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();
        $container->instance('app', new App($container, ''));

        return $container;
    }
}
