<?php

declare(strict_types=1);

namespace Tests\Http;

use Leevel\Http\Request;
use Leevel\Kernel\Utils\Api;
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
\App::make('request')->get($key, mixed $default = null);
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

使用代理

``` php
\Leevel\Router\Proxy\Request::proxy()->get(string $key, ?$default = null);
\Leevel\Router\Proxy\Request::proxy()->all(): array;
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
        self::assertSame(['foo' => 'bar', 'hello' => 'world'], $request->query->all());
        self::assertSame('content', $request->getContent());
    }

    public function testCreateFromSymfonyRequestWithSelfRequest(): void
    {
        $selfRequest = new Request(['foo' => 'bar', 'hello' => 'world'], [], [], [], [], [], 'content');
        $request = Request::createFromSymfonyRequest($selfRequest);

        $this->assertInstanceof(Request::class, $request);
        self::assertSame(['foo' => 'bar', 'hello' => 'world'], $request->query->all());
        self::assertSame('content', $request->getContent());
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
        self::assertSame(['request' => '2', 'query' => '1', 'attributes' => '3'], $request->all());

        $request = new Request(['foo' => '1'], ['foo' => '2'], ['foo' => '3']);
        self::assertSame(['foo' => '2'], $request->all());
    }

    #[Api([
        'zh-CN:title' => 'exists 请求是否包含非空',
    ])]
    public function testExists(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        self::assertTrue($request->exists(['foo']));
        self::assertTrue($request->exists(['foo', 'hello']));
        self::assertFalse($request->exists(['notFound']));
    }

    #[Api([
        'zh-CN:title' => 'only 取得给定的 keys 数据',
    ])]
    public function testOnly(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        self::assertSame(['foo' => 'bar'], $request->only(['foo']));
        self::assertSame(['foo' => 'bar', 'hello' => 'world'], $request->only(['foo', 'hello']));
        self::assertSame(['foo' => 'bar', 'not' => null], $request->only(['foo', 'not']));
    }

    #[Api([
        'zh-CN:title' => 'except 取得排除给定的 keys 数据',
    ])]
    public function testExcept(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        self::assertSame(['hello' => 'world'], $request->except(['foo']));
        self::assertSame([], $request->except(['foo', 'hello']));
        self::assertSame(['hello' => 'world'], $request->except(['foo', 'not']));
    }

    #[Api([
        'zh-CN:title' => 'isConsole 是否为 PHP 运行模式命令行',
    ])]
    public function testIsConsole(): void
    {
        $request = new Request();
        self::assertTrue($request->isConsole());
    }

    #[Api([
        'zh-CN:title' => 'isCgi 是否为 PHP 运行模式 cgi',
    ])]
    public function testIsCgi(): void
    {
        $request = new Request();
        self::assertFalse($request->isCgi());
    }

    #[Api([
        'zh-CN:title' => 'isPjax 是否为 Pjax 请求行为',
    ])]
    public function testIsPjax(): void
    {
        $request = new Request();

        self::assertFalse($request->isPjax());
        $request->request->set(Request::VAR_PJAX, true);
        self::assertTrue($request->isPjax());
    }

    #[Api([
        'zh-CN:title' => 'isAcceptAny 是否为接受任何请求，支持伪装',
    ])]
    public function testIsAcceptJson(): void
    {
        $request = new Request();

        self::assertFalse($request->isRealAcceptJson());
        self::assertFalse($request->isAcceptJson());

        $request->headers->set('accept', 'application/json, text/plain, */*');
        self::assertTrue($request->isRealAcceptJson());
        self::assertTrue($request->isAcceptJson());
        $request->headers->remove('accept');

        self::assertFalse($request->isRealAcceptJson());
        self::assertFalse($request->isAcceptJson());

        // (isAjax && !isPjax) && isAcceptAny
        $request->request->set(Request::VAR_AJAX, 1);
        self::assertFalse($request->isRealAcceptJson());
        self::assertTrue($request->isAcceptJson());
        $request->request->remove(Request::VAR_AJAX);

        // 伪装
        $request->query->set(Request::VAR_ACCEPT_JSON, '1');
        self::assertTrue($request->isAcceptJson());
        self::assertFalse($request->isRealAcceptJson());
    }

    public function testIsRealAcceptJsonIsFalse(): void
    {
        $request = new Request();

        self::assertFalse($request->isRealAcceptJson());
        self::assertFalse($request->isAcceptJson());

        $request->headers->set('accept', 'application/pdf, text/plain, */*');
        self::assertFalse($request->isRealAcceptJson());
        self::assertFalse($request->isAcceptJson());
    }

    #[Api([
        'zh-CN:title' => 'isAcceptAny 是否为接受任何请求',
    ])]
    public function testIsAcceptAny(): void
    {
        $request = new Request();

        self::assertTrue($request->isAcceptAny());

        $request->headers->set('accept', 'application/json');
        self::assertFalse($request->isAcceptAny());

        $request->headers->set('accept', '*/*');
        self::assertTrue($request->isAcceptAny());
    }

    #[Api([
        'zh-CN:title' => 'getEnter 获取入口文件',
    ])]
    public function testGetEnter(): void
    {
        $request = new Request();
        self::assertSame('', $request->getEnter());
    }

    public function testGetEnterReturnsCorrectDirectoryInNonConsoleEnvironment(): void
    {
        // 测试在非控制台环境下，是否返回正确的目录路径
        // 创建一个模拟对象，模拟 $this->isConsole() 方法返回 false
        $mock = $this->getMockBuilder(Request::class)
            ->onlyMethods(['isConsole', 'getScriptName'])
            ->getMock()
        ;
        $mock->expects(self::once())
            ->method('isConsole')
            ->willReturn(false)
        ;

        // 设置 $this->getScriptName() 方法返回指定的文件路径
        $mock->method('getScriptName')
            ->willReturn('/path/to/my_script.php')
        ;

        // 断言 getEnter() 方法返回的目录路径是否与预期相符
        $expectedDirectory = '/path/to';
        self::assertEquals($expectedDirectory, $mock->getEnter());
    }

    public function testGetEnterReturnsEmptyStringInConsoleEnvironment(): void
    {
        // 测试在控制台环境下，是否返回空字符串
        // 创建一个模拟对象，模拟 $this->isConsole() 方法返回 true
        $mock = $this->getMockBuilder(Request::class)
            ->onlyMethods(['isConsole'])
            ->getMock()
        ;
        $mock->expects(self::once())
            ->method('isConsole')
            ->willReturn(true)
        ;

        // 断言 getEnter() 方法返回的是否为空字符串
        self::assertEquals('', $mock->getEnter());
    }

    #[Api([
        'zh-CN:title' => 'setPathInfo 设置 pathInfo',
    ])]
    public function testSetPathInfo(): void
    {
        $request = new Request();
        self::assertSame('/', $request->getPathInfo());
        $request->setPathInfo('/foo/bar');
        self::assertSame('/foo/bar', $request->getPathInfo());
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
        self::assertSame(['foo' => 'bar', 'hello' => 'world'], $request->toArray());
    }
}
