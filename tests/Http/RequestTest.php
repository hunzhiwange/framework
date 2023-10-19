<?php

declare(strict_types=1);

namespace Tests\Http;

use Leevel\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'HTTP Request',
    'path' => 'component/http/request',
    'zh-CN:description' => <<<'EOT'
QueryPHP 请求对象构建在 Symfony HttpFoundation 之上，增加了少量的功能。

## 使用方式

使用容器 request 服务

``` php
\App::make('request')->get($key, $default = null);
\App::make('request')->all(): array;
```

依赖注入

``` php
class Demo
{
    private \Leevel\Http\Request $request;

    public function __construct(\Leevel\Http\Request $request)
    {
        $this->request = $request;
    }
}
```

使用静态代理

``` php
\Leevel\Router\Proxy\Request::get(string $key, $default = null);
\Leevel\Router\Proxy\Request::all(): array;
```

::: warning 注意
为了一致性或者更好与 RoadRunner 对接，请统一使用请求对象处理输入，避免直接使用 `$_GET`、`$_POST`,`$_COOKIE`,`$_FILES`,`$_SERVER` 等全局变量。
:::
EOT,
])]
final class RequestTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'createFromSymfonyRequest 从 Symfony 请求创建 Leevel 请求',
    ])]
    public function testCreateFromSymfonyRequest(): void
    {
        $symfonyRequest = new SymfonyRequest(['foo' => 'bar', 'hello' => 'world'], [], [], [], [], [], 'content');
        $request = Request::createFromSymfonyRequest($symfonyRequest);

        $this->assertInstanceof(Request::class, $request);
        static::assertSame(['foo' => 'bar', 'hello' => 'world'], $request->query->all());
        static::assertSame('content', $request->getContent());
    }

    public function testCreateFromSymfonyRequestWithSelfRequest(): void
    {
        $selfRequest = new Request(['foo' => 'bar', 'hello' => 'world'], [], [], [], [], [], 'content');
        $request = Request::createFromSymfonyRequest($selfRequest);

        $this->assertInstanceof(Request::class, $request);
        static::assertSame(['foo' => 'bar', 'hello' => 'world'], $request->query->all());
        static::assertSame('content', $request->getContent());
    }

    #[Api([
        'zh-CN:title' => 'all 获取所有请求参数',
        'zh-CN:description' => <<<'EOT'
* 包含 request、query 和 attributes
 * 优先级从高到底依次为 attributes、query 和 request，优先级高的会覆盖优先级低的参数
EOT,
    ])]
    public function testAll(): void
    {
        $request = new Request(['query' => '1'], ['request' => '2'], ['attributes' => '3']);
        static::assertSame(['request' => '2', 'query' => '1', 'attributes' => '3'], $request->all());

        $request = new Request(['foo' => '1'], ['foo' => '2'], ['foo' => '3']);
        static::assertSame(['foo' => '2'], $request->all());
    }

    #[Api([
        'zh-CN:title' => 'exists 请求是否包含非空',
    ])]
    public function testExists(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        static::assertTrue($request->exists(['foo']));
        static::assertTrue($request->exists(['foo', 'hello']));
        static::assertFalse($request->exists(['notFound']));
    }

    #[Api([
        'zh-CN:title' => 'only 取得给定的 keys 数据',
    ])]
    public function testOnly(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        static::assertSame(['foo' => 'bar'], $request->only(['foo']));
        static::assertSame(['foo' => 'bar', 'hello' => 'world'], $request->only(['foo', 'hello']));
        static::assertSame(['foo' => 'bar', 'not' => null], $request->only(['foo', 'not']));
    }

    #[Api([
        'zh-CN:title' => 'except 取得排除给定的 keys 数据',
    ])]
    public function testExcept(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        static::assertSame(['hello' => 'world'], $request->except(['foo']));
        static::assertSame([], $request->except(['foo', 'hello']));
        static::assertSame(['hello' => 'world'], $request->except(['foo', 'not']));
    }

    #[Api([
        'zh-CN:title' => 'isConsole 是否为 PHP 运行模式命令行',
    ])]
    public function testIsConsole(): void
    {
        $request = new Request();
        static::assertTrue($request->isConsole());
    }

    #[Api([
        'zh-CN:title' => 'isCgi 是否为 PHP 运行模式 cgi',
    ])]
    public function testIsCgi(): void
    {
        $request = new Request();
        static::assertFalse($request->isCgi());
    }

    #[Api([
        'zh-CN:title' => 'isPjax 是否为 Pjax 请求行为',
    ])]
    public function testIsPjax(): void
    {
        $request = new Request();

        static::assertFalse($request->isPjax());
        $request->request->set(Request::VAR_PJAX, true);
        static::assertTrue($request->isPjax());
    }

    #[Api([
        'zh-CN:title' => 'isAcceptAny 是否为接受任何请求，支持伪装',
    ])]
    public function testIsAcceptJson(): void
    {
        $request = new Request();

        static::assertFalse($request->isRealAcceptJson());
        static::assertFalse($request->isAcceptJson());

        $request->headers->set('accept', 'application/json, text/plain, */*');
        static::assertTrue($request->isRealAcceptJson());
        static::assertTrue($request->isAcceptJson());
        $request->headers->remove('accept');

        static::assertFalse($request->isRealAcceptJson());
        static::assertFalse($request->isAcceptJson());

        // (isAjax && !isPjax) && isAcceptAny
        $request->request->set(Request::VAR_AJAX, 1);
        static::assertFalse($request->isRealAcceptJson());
        static::assertTrue($request->isAcceptJson());
        $request->request->remove(Request::VAR_AJAX);

        // 伪装
        $request->query->set(Request::VAR_ACCEPT_JSON, '1');
        static::assertTrue($request->isAcceptJson());
        static::assertFalse($request->isRealAcceptJson());
    }

    public function testIsRealAcceptJsonIsFalse(): void
    {
        $request = new Request();

        static::assertFalse($request->isRealAcceptJson());
        static::assertFalse($request->isAcceptJson());

        $request->headers->set('accept', 'application/pdf, text/plain, */*');
        static::assertFalse($request->isRealAcceptJson());
        static::assertFalse($request->isAcceptJson());
    }

    #[Api([
        'zh-CN:title' => 'isAcceptAny 是否为接受任何请求',
    ])]
    public function testIsAcceptAny(): void
    {
        $request = new Request();

        static::assertTrue($request->isAcceptAny());

        $request->headers->set('accept', 'application/json');
        static::assertFalse($request->isAcceptAny());

        $request->headers->set('accept', '*/*');
        static::assertTrue($request->isAcceptAny());
    }

    #[Api([
        'zh-CN:title' => 'getEnter 获取入口文件',
    ])]
    public function testGetEnter(): void
    {
        $request = new Request();
        static::assertSame('', $request->getEnter());
    }

    public function testGetEnterReturnsCorrectDirectoryInNonConsoleEnvironment(): void
    {
        // 测试在非控制台环境下，是否返回正确的目录路径
        // 创建一个模拟对象，模拟 $this->isConsole() 方法返回 false
        $mock = $this->getMockBuilder(Request::class)
            ->onlyMethods(['isConsole', 'getScriptName'])
            ->getMock()
        ;
        $mock->expects(static::once())
            ->method('isConsole')
            ->willReturn(false)
        ;

        // 设置 $this->getScriptName() 方法返回指定的文件路径
        $mock->method('getScriptName')
            ->willReturn('/path/to/my_script.php')
        ;

        // 断言 getEnter() 方法返回的目录路径是否与预期相符
        $expectedDirectory = '/path/to';
        static::assertEquals($expectedDirectory, $mock->getEnter());
    }

    public function testGetEnterReturnsEmptyStringInConsoleEnvironment(): void
    {
        // 测试在控制台环境下，是否返回空字符串
        // 创建一个模拟对象，模拟 $this->isConsole() 方法返回 true
        $mock = $this->getMockBuilder(Request::class)
            ->onlyMethods(['isConsole'])
            ->getMock()
        ;
        $mock->expects(static::once())
            ->method('isConsole')
            ->willReturn(true)
        ;

        // 断言 getEnter() 方法返回的是否为空字符串
        static::assertEquals('', $mock->getEnter());
    }

    #[Api([
        'zh-CN:title' => 'setPathInfo 设置 pathInfo',
    ])]
    public function testSetPathInfo(): void
    {
        $request = new Request();
        static::assertSame('/', $request->getPathInfo());
        $request->setPathInfo('/foo/bar');
        static::assertSame('/foo/bar', $request->getPathInfo());
    }

    #[Api([
        'zh-CN:title' => 'toArray 对象转数组',
        'zh-CN:description' => <<<'EOT'
Request 请求对象实现了 `\Leevel\Support\IArray` 接口。
EOT,
    ])]
    public function testToArray(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        static::assertSame(['foo' => 'bar', 'hello' => 'world'], $request->toArray());
    }
}
