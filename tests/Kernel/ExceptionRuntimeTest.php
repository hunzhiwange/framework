<?php

declare(strict_types=1);

namespace Tests\Kernel;

use Leevel\Database\Ddd\EntityNotFoundException;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\Request;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Exceptions\HttpException;
use Leevel\Kernel\Exceptions\InternalServerErrorHttpException;
use Leevel\Kernel\Exceptions\MethodNotAllowedHttpException;
use Leevel\Kernel\Exceptions\NotFoundHttpException;
use Leevel\Kernel\Exceptions\Runtime;
use Leevel\Kernel\Utils\Api;
use Leevel\Log\ILog;
use Leevel\Option\Option;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '异常运行时',
    'path' => 'architecture/kernel/exceptionruntime',
    'zh-CN:description' => <<<'EOT'
QueryPHP 系统发生的异常统一由异常运行时进行管理，处理异常上报和返回异常响应。

**异常运行时接口**

``` php
{[file_get_contents('vendor/hunzhiwange/framework/src/Leevel/Kernel/Exceptions/IRuntime.php')]}
```

**默认异常运行时提供两个抽象方法**

**getHttpExceptionView 原型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Kernel\Exceptions\Runtime::class, 'getHttpExceptionView', 'define')]}
```

**getDefaultHttpExceptionView 原型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Kernel\Exceptions\Runtime::class, 'getDefaultHttpExceptionView', 'define')]}
```

只需要实现，即可轻松接入，例如应用中的 `\App\Exceptions\Runtime` 实现。

``` php
{[file_get_contents('app/Infra/Exceptions/Runtime.php')]}
```
EOT,
    'zh-CN:note' => <<<'EOT'
异常运行时设计为可替代，只需要实现 `\Leevel\Kernel\Exceptions\IRuntime` 即可，然后在入口文件替换即可。
EOT,
])]
final class ExceptionRuntimeTest extends TestCase
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
        'zh-CN:title' => '基本使用',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Kernel\AppRuntime**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\AppRuntime::class)]}
```

**Tests\Kernel\Runtime11**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Runtime11::class)]}
```

**Tests\Kernel\Exception1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception1::class)]}
```
EOT,
    ])]
    public function testBaseUse(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);

        $option = new Option([
            'app' => [
                ':composer' => [
                    'i18ns' => [
                        'extend',
                    ],
                ],
            ],
            'i18n' => [
                'default' => 'en-US',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $log = $this->createMock(ILog::class);

        static::assertNull($log->error('hello world', []));

        $container->singleton(ILog::class, function () use ($log) {
            return $log;
        });

        $runtime = new Runtime11($app);

        $e = new Exception1('hello world');

        static::assertNull($runtime->report($e));
    }

    #[Api([
        'zh-CN:title' => 'report 自定义异常上报',
        'zh-CN:description' => <<<'EOT'
异常提供 `report` 方法即实现自定义异常上报。

**fixture 定义**

**Tests\Kernel\Exception2**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception2::class)]}
```
EOT,
    ])]
    public function testExceptionItSelfWithReport(): void
    {
        $app = new AppRuntime(new Container(), __DIR__.'/app');

        $runtime = new Runtime11($app);

        $e = new Exception2('hello world');

        static::assertArrayNotHasKey('testExceptionItSelfWithReport', $_SERVER);

        static::assertNull($runtime->report($e));

        static::assertSame(1, $_SERVER['testExceptionItSelfWithReport']);

        unset($_SERVER['testExceptionItSelfWithReport']);
    }

    #[Api([
        'zh-CN:title' => 'reportable 异常是否需要上报',
        'zh-CN:description' => <<<'EOT'
默认可上报，reportable 返回 true 可以会上报。

**fixture 定义**

**Tests\Kernel\ExceptionCanReportable**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\ExceptionCanReportable::class)]}
```
EOT,
    ])]
    public function testExceptionReportable(): void
    {
        $app = new AppRuntime(new Container(), __DIR__.'/app');
        $runtime = new Runtime11($app);
        $e = new ExceptionCanReportable('hello world');
        static::assertArrayNotHasKey('testExceptionReportable', $_SERVER);
        static::assertNull($runtime->report($e));
        static::assertSame(1, $_SERVER['testExceptionReportable']);
        unset($_SERVER['testExceptionReportable']);
    }

    #[Api([
        'zh-CN:title' => 'reportable 异常是否需要上报不可上报例子',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Kernel\ExceptionCannotReportable**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\ExceptionCannotReportable::class)]}
```
EOT,
    ])]
    public function testExceptionReportableIsFalse(): void
    {
        $app = new AppRuntime(new Container(), __DIR__.'/app');
        $runtime = new Runtime11($app);
        $e = new ExceptionCannotReportable('hello world');
        static::assertArrayNotHasKey('testExceptionReportableIsFalse', $_SERVER);
        static::assertNull($runtime->report($e));
        static::assertArrayNotHasKey('testExceptionReportableIsFalse', $_SERVER);
    }

    #[Api([
        'zh-CN:title' => 'render 开启调试模式的异常渲染',
    ])]
    public function testRender(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isAcceptJson')->willReturn(false);
        static::assertFalse($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug' => true,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime11($app);

        $e = new Exception1('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));
        static::assertStringContainsString('Tests\\Kernel\\Exception1: hello world in file', $resultResponse->getContent());
        static::assertSame(500, $resultResponse->getStatusCode());
    }

    #[Api([
        'zh-CN:title' => 'renderForConsole 命令行渲染',
    ])]
    public function testRenderForConsole(): void
    {
        $app = new AppRuntimeForConsole($container = new Container(), __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);

        $option = new Option([
            'app' => [
                ':composer' => [
                    'i18ns' => [
                        'extend',
                    ],
                ],
            ],
            'i18n' => [
                'default' => 'en-US',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $log = $this->createMock(ILog::class);

        static::assertNull($log->error('hello world', []));

        $container->singleton(ILog::class, function () use ($log) {
            return $log;
        });

        $runtime = new Runtime11($app);

        $e = new Exception1('hello world');

        $runtime->renderForConsole(new ConsoleOutput(), $e);
    }

    #[Api([
        'zh-CN:title' => 'render 自定义异常渲染',
        'zh-CN:description' => <<<'EOT'
异常提供 `render` 方法即实现自定义异常渲染。

**fixture 定义**

**Tests\Kernel\Exception3**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception3::class)]}
```
EOT,
    ])]
    public function testRenderWithCustomRenderMethod(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isAcceptJson')->willReturn(false);
        static::assertFalse($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug' => true,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime11($app);

        $e = new Exception3('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        static::assertSame('hello world', $resultResponse->getContent());
        static::assertSame(500, $resultResponse->getStatusCode());
    }

    #[Api([
        'zh-CN:title' => 'render 自定义异常渲染直接返回响应对象',
        'zh-CN:description' => <<<'EOT'
异常提供 `render` 方法即实现自定义异常渲染。

**fixture 定义**

**Tests\Kernel\Exception4**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception4::class)]}
```
EOT,
    ])]
    public function testRenderWithCustomRenderMethod2(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isAcceptJson')->willReturn(false);
        static::assertFalse($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug' => true,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime11($app);

        $e = new Exception4('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        static::assertSame('foo bar', $resultResponse->getContent());
        static::assertSame(500, $resultResponse->getStatusCode());
    }

    #[Api([
        'zh-CN:title' => 'render 异常渲染直接返回 JSON 数据',
    ])]
    public function testRenderToJson(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isAcceptJson')->willReturn(true);
        static::assertTrue($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug' => true,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime11($app);

        $e = new Exception1('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        static::assertIsArray($content = json_decode($resultResponse->getContent(), true));
        static::assertArrayHasKey('error', $content);
        static::assertSame('Tests\\Kernel\\Exception1', $content['error']['type']);
        static::assertSame('hello world', $content['error']['message']);
        static::assertSame(500, $resultResponse->getStatusCode());
    }

    #[Api([
        'zh-CN:title' => 'render 自定义异常渲染直接返回支持转 JSON 响应的数据',
        'zh-CN:description' => <<<'EOT'
异常提供 `render` 方法即实现自定义异常渲染。

**fixture 定义**

**Tests\Kernel\Exception5**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception5::class)]}
```
EOT,
    ])]
    public function testRenderWithCustomRenderMethodToJson(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isAcceptJson')->willReturn(true);
        static::assertTrue($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug' => true,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime11($app);

        $e = new Exception5('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        static::assertSame('{"foo":"bar"}', $resultResponse->getContent());
        static::assertSame(500, $resultResponse->getStatusCode());
    }

    #[Api([
        'zh-CN:title' => 'render HTTP 500 异常响应渲染',
        'zh-CN:description' => <<<'EOT'
异常提供 `render` 方法即实现自定义异常渲染。

**fixture 定义**

**Tests\Kernel\Runtime22**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Runtime22::class)]}
```

**Tests\Kernel\Exception6**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception6::class)]}
```

**异常模板 tests/Kernel/assert/layout.php **

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/assert/layout.php')]}
```

**异常模板 tests/Kernel/assert/500.php **

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/assert/500.php')]}
```
EOT,
    ])]
    public function testRendorWithHttpExceptionView(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $option = new Option([
            'app' => [
                'debug' => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime22($app);

        $e = new Exception6('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $this->invokeTestMethod($runtime, 'rendorWithHttpExceptionView', [$e]));

        $content = $resultResponse->getContent();

        static::assertStringContainsString('<div id="status-code">500</div>', $content);
        static::assertStringContainsString('<p id="title">服务器内部错误</p>', $content);
        static::assertStringContainsString('<p id="sub-title">0 服务器遇到错误，无法完成请求</p>', $content);
        static::assertSame(500, $resultResponse->getStatusCode());
    }

    #[Api([
        'zh-CN:title' => 'render HTTP 异常响应渲染使用默认异常模板的例子',
        'zh-CN:description' => <<<'EOT'
异常提供 `render` 方法即实现自定义异常渲染。

**fixture 定义**

**Tests\Kernel\Exception7**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception7::class)]}
```

**异常模板 tests/Kernel/assert/404.php **

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/assert/404.php')]}
```
EOT,
    ])]
    public function testRendorWithHttpExceptionViewFor404(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $option = new Option([
            'app' => [
                'debug' => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime22($app);

        $e = new Exception7('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $this->invokeTestMethod($runtime, 'rendorWithHttpExceptionView', [$e]));

        $content = $resultResponse->getContent();

        static::assertStringContainsString('<div id="status-code">404</div>', $content);
        static::assertStringContainsString('<p id="title">页面未找到</p>', $content);
        static::assertStringContainsString('<p id="sub-title">0 用户发出的请求针对的是不存在的页面</p>', $content);
        static::assertSame(404, $resultResponse->getStatusCode());
    }

    #[Api([
        'zh-CN:title' => 'render HTTP 异常响应渲染',
        'zh-CN:description' => <<<'EOT'
异常提供 `render` 方法即实现自定义异常渲染。

**fixture 定义**

**Tests\Kernel\Runtime3**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Runtime3::class)]}
```

**Tests\Kernel\Exception8**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception8::class)]}
```

**异常模板 tests/Kernel/assert/default.php **

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/assert/default.php')]}
```
EOT,
    ])]
    public function testRendorWithHttpExceptionViewButNotFoundViewAndWithDefaultView(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $option = new Option([
            'app' => [
                'debug' => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime3($app);

        $e = new Exception8('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $this->invokeTestMethod($runtime, 'rendorWithHttpExceptionView', [$e]));

        $content = $resultResponse->getContent();

        static::assertStringContainsString('<div id="status-code">405</div>', $content);
        static::assertStringContainsString('Tests\\Kernel\\Exception8<div class="file">#FILE', $content);
        static::assertStringContainsString('<p id="sub-title">0 hello world</p>', $content);
        static::assertSame(405, $resultResponse->getStatusCode());
    }

    public function testRendorWithHttpExceptionViewButNotFoundView(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $option = new Option([
            'app' => [
                'debug' => true,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime22($app);

        $e = new Exception8('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $this->invokeTestMethod($runtime, 'rendorWithHttpExceptionView', [$e]));

        $content = $resultResponse->getContent();
        $contentNew = '<div id="status-code">405</div>

<div id="content">
    <p id="title">方法禁用</p>
    <p id="sub-title">0 hello world</p>
</div>';

        static::assertSame($contentNew, $content);
        static::assertSame(405, $resultResponse->getStatusCode());
    }

    #[Api([
        'zh-CN:title' => 'render 调试关闭异常渲染',
    ])]
    public function testRenderWithDebugIsOff(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isAcceptJson')->willReturn(false);
        static::assertFalse($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug' => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime22($app);

        $e = new Exception1('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        $content = $resultResponse->getContent();

        static::assertStringContainsString('<div id="status-code">500</div>', $content);
        static::assertStringContainsString('<p id="title">服务器内部错误</p>', $content);
        static::assertStringContainsString('<p id="sub-title">0 服务器遇到错误，无法完成请求</p>', $content);
        static::assertSame(500, $resultResponse->getStatusCode());
    }

    #[Api([
        'zh-CN:title' => 'render 调试开启异常渲染',
    ])]
    public function testRenderWithDebugIsOn(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isAcceptJson')->willReturn(false);
        static::assertFalse($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug' => true,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime22($app);

        $e = new Exception1('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        $content = $resultResponse->getContent();

        static::assertStringContainsString('Tests\\Kernel\\Exception1: hello world', $content);
        static::assertSame(500, $resultResponse->getStatusCode());
    }

    #[Api([
        'zh-CN:title' => 'render JSON 异常响应渲染',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Kernel\Runtime22**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Runtime22::class)]}
```

**Tests\Kernel\Exception1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception1::class)]}
```
EOT,
    ])]
    public function test1(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isAcceptJson')->willReturn(true);
        static::assertTrue($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug' => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime22($app);

        $e = new Exception1('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        $content = $resultResponse->getContent();

        static::assertStringContainsString('{"error":{"code":0,"message":"hello world"}}', $content);
        static::assertSame(500, $resultResponse->getStatusCode());
    }

    #[Api([
        'zh-CN:title' => 'render JSON 异常响应渲染',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Kernel\Runtime22**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Runtime22::class)]}
```

**Tests\Kernel\Exception1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception1::class)]}
```
EOT,
    ])]
    public function test2(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isAcceptJson')->willReturn(true);
        static::assertTrue($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug' => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime22($app);

        $e = new Exception1('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        $content = $resultResponse->getContent();

        static::assertStringContainsString('{"error":{"code":0,"message":"hello world"}}', $content);
        static::assertSame(500, $resultResponse->getStatusCode());
    }

    #[Api([
        'zh-CN:title' => 'render JSON 异常响应渲染',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Kernel\Runtime33**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Runtime33::class)]}
```

**Tests\Kernel\Exception8**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception8::class)]}
```
EOT,
    ])]
    public function test3(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isAcceptJson')->willReturn(true);
        static::assertTrue($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug' => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime33($app);

        $e = new Exception8('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        $content = $resultResponse->getContent();

        static::assertStringContainsString('{"error":{"title":"方法禁用","code":0,"message":"hello world","duration":5}}', $content);
        static::assertSame(405, $resultResponse->getStatusCode());
    }

    public function testRendorWithHttpExceptionViewButNotFoundViewAndWithDefaultViewButNotStill(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            sprintf('Exception file %s is not exist.', __DIR__.'/assert/notFoundDefault.php')
        );

        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $option = new Option([
            'app' => [
                'debug' => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime4($app);

        $e = new Exception8('hello world');
        $this->invokeTestMethod($runtime, 'rendorWithHttpExceptionView', [$e]);
    }

    public function testRenderForEntityNotFoundException(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isAcceptJson')->willReturn(false);
        static::assertFalse($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug' => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime22($app);

        $e = new EntityNotFoundException('hello world');

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        $content = $resultResponse->getContent();

        static::assertStringContainsString('<div id="status-code">404</div>', $content);
        static::assertStringContainsString('<p id="title">页面未找到</p>', $content);
        static::assertStringContainsString('<p id="sub-title">0 用户发出的请求针对的是不存在的页面</p>', $content);
        static::assertSame(404, $resultResponse->getStatusCode());
    }

    public function testReportToLogError(): void
    {
        $app = new AppRuntime($container = new Container(), __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);

        $option = new Option([
            'app' => [
                ':composer' => [
                    'i18ns' => [
                        'extend',
                    ],
                ],
            ],
            'i18n' => [
                'default' => 'en-US',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime11($app);

        $e = new Exception1('hello world');

        static::assertNull($runtime->report($e));
    }

    public function test4(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Exception file not_found.php is not exist.'
        );

        $app = new AppRuntime(new Container(), __DIR__.'/app');
        $runtime = new Runtime11($app);
        $this->invokeTestMethod($runtime, 'renderJsonWithFile', ['not_found.php', []]);
    }
}

class AppRuntime extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class AppRuntimeForNotDebug extends Apps
{
    public function isDebug(): bool
    {
        return false;
    }

    protected function registerBaseProvider(): void
    {
    }
}

class AppRuntimeForConsole extends Apps
{
    public function isConsole(): bool
    {
        return true;
    }

    protected function registerBaseProvider(): void
    {
    }
}

class Runtime11 extends Runtime
{
    public function getHttpExceptionView(HttpException $e): string
    {
        return '';
    }

    public function getDefaultHttpExceptionView(): string
    {
        return '';
    }

    public function getJsonExceptionView(HttpException $e): string
    {
        return '';
    }

    public function getDefaultJsonExceptionData(\Throwable $e): array
    {
        return [
            'error' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ];
    }
}

class Runtime22 extends Runtime
{
    public function getHttpExceptionView(HttpException $e): string
    {
        return __DIR__.'/assert/'.$e->getStatusCode().'.php';
    }

    public function getDefaultHttpExceptionView(): string
    {
        return '';
    }

    public function getJsonExceptionView(HttpException $e): string
    {
        return '';
    }

    public function getDefaultJsonExceptionData(\Throwable $e): array
    {
        return [
            'error' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ];
    }
}

class Runtime33 extends Runtime
{
    public function getHttpExceptionView(HttpException $e): string
    {
        return __DIR__.'/assert/'.$e->getStatusCode().'.php';
    }

    public function getDefaultHttpExceptionView(): string
    {
        return '';
    }

    public function getJsonExceptionView(HttpException $e): string
    {
        return __DIR__.'/assert/'.$e->getStatusCode().'.php';
    }

    public function getDefaultJsonExceptionData(\Throwable $e): array
    {
        return [
            'error' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ];
    }
}

class Runtime3 extends Runtime
{
    public function getHttpExceptionView(HttpException $e): string
    {
        return __DIR__.'/assert/notFound.php';
    }

    public function getDefaultHttpExceptionView(): string
    {
        return __DIR__.'/assert/default.php';
    }

    public function getJsonExceptionView(HttpException $e): string
    {
        return '';
    }

    public function getDefaultJsonExceptionData(\Throwable $e): array
    {
        return [
            'error' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ];
    }
}

class Runtime4 extends Runtime
{
    public function getHttpExceptionView(HttpException $e): string
    {
        return __DIR__.'/assert/notFound.php';
    }

    public function getDefaultHttpExceptionView(): string
    {
        return __DIR__.'/assert/notFoundDefault.php';
    }

    public function getJsonExceptionView(HttpException $e): string
    {
        return '';
    }

    public function getDefaultJsonExceptionData(\Throwable $e): array
    {
        return [
            'error' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ];
    }
}

class Exception1 extends \Exception
{
}

class Exception2 extends \Exception
{
    public function report(): void
    {
        $_SERVER['testExceptionItSelfWithReport'] = 1;
    }
}

class Exception3 extends \Exception
{
    public function render(Request $request, \Exception $e): string
    {
        return 'hello world';
    }
}

class Exception4 extends \Exception
{
    public function render(Request $request, \Exception $e): Response
    {
        return new Response('foo bar', 500);
    }
}

class Exception5 extends \Exception
{
    public function render(Request $request, \Exception $e): array
    {
        return ['foo' => 'bar'];
    }
}

class Exception6 extends InternalServerErrorHttpException
{
}

class Exception7 extends NotFoundHttpException
{
}

class Exception8 extends MethodNotAllowedHttpException
{
    protected bool $errorBlocking = false;
}

class ExceptionCanReportable extends \Exception
{
    public function reportable(): bool
    {
        return true;
    }

    public function report(): void
    {
        $_SERVER['testExceptionReportable'] = 1;
    }
}

class ExceptionCannotReportable extends \Exception
{
    public function reportable(): bool
    {
        return false;
    }

    public function report(): void
    {
        $_SERVER['testExceptionReportableIsFalse'] = 1;
    }
}
