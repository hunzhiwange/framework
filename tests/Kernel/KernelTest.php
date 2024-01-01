<?php

declare(strict_types=1);

namespace Tests\Kernel;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\JsonResponse;
use Leevel\Http\Request;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Exceptions\HttpException;
use Leevel\Kernel\Exceptions\IRuntime;
use Leevel\Kernel\Exceptions\Runtime;
use Leevel\Kernel\IApp;
use Leevel\Kernel\IKernel;
use Leevel\Kernel\Kernel;
use Leevel\Kernel\Utils\Api;
use Leevel\Log\ILog;
use Leevel\Option\IOption;
use Leevel\Router\IRouter;
use Symfony\Component\HttpFoundation\Response;
use Tests\Kernel\Middlewares\Demo1;
use Tests\Kernel\Middlewares\Demo2;
use Tests\Kernel\Middlewares\Demo3;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '内核',
    'path' => 'architecture/kernel',
    'zh-CN:description' => <<<'EOT'
QueryPHP 流程为入口接受 HTTP 请求，经过内核 kernel 传入请求，经过路由解析调用控制器执行业务，最后返回响应结果。

入口文件 `www/index.php`

``` php
{[file_get_contents('www/index.php')]}
```

内核通过 \Leevel\Kernel\Kernel 的 handle 方法来实现请求。

**handle 原型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Kernel\Kernel::class, 'handle', 'define')]}
```
EOT,
    'zh-CN:note' => <<<'EOT'
内核设计为可替代，只需要实现 `\Leevel\Kernel\IKernel` 即可，然后在入口文件替换即可。
EOT,
])]
final class KernelTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     */
    #[Api([
        'zh-CN:title' => '基本使用',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Kernel\Kernel1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Kernel1::class)]}
```

**Tests\Kernel\DemoBootstrapForKernel**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\DemoBootstrapForKernel::class)]}
```
EOT,
    ])]
    public function testBaseUse(bool $debug): void
    {
        $app = new AppKernel($container = new Container(), '');
        $container->instance('app', $app);

        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $router = $this->createRouter($response);
        $this->createOption($container, $debug);
        $this->createLog($container);
        $this->createRuntime($container);

        $kernel = new Kernel1($app, $router);
        $this->assertInstanceof(IKernel::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());
        $this->assertInstanceof(Response::class, $resultResponse = $kernel->handle($request));
        $kernel->terminate($request, $resultResponse);
        static::assertTrue($GLOBALS['DemoBootstrapForKernel']);
        unset($GLOBALS['DemoBootstrapForKernel']);
    }

    public static function baseUseProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    #[Api([
        'zh-CN:title' => 'JSON 响应例子',
    ])]
    public function testWithResponseIsJson(): void
    {
        $app = new AppKernel($container = new Container(), '');
        $container->instance('app', $app);

        $request = $this->createMock(Request::class);
        $response = new JsonResponse(['foo' => 'bar']);

        $router = $this->createRouter($response);
        $this->createOption($container, true);
        $this->createLog($container);
        $this->createRuntime($container);

        $kernel = new Kernel1($app, $router);
        $this->assertInstanceof(IKernel::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());

        $this->assertInstanceof(Response::class, $resultResponse = $kernel->handle($request));
        static::assertSame('{"foo":"bar"}', $resultResponse->getContent());
    }

    #[Api([
        'zh-CN:title' => '异常处理',
        'zh-CN:description' => <<<'EOT'
路由抛出异常，返回异常响应。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Kernel\KernelTest::class, 'createRouterWithException')]}
```
EOT,
    ])]
    public function testRouterWillThrowException(): void
    {
        $app = new AppKernel($container = new Container(), '');
        $container->instance('app', $app);

        $request = $this->createMock(Request::class);

        $router = $this->createRouterWithException();
        $this->createOption($container, true);
        $this->createLog($container);
        $this->createRuntimeWithRender($container);

        $kernel = new Kernel1($app, $router);
        $this->assertInstanceof(IKernel::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());

        $this->assertInstanceof(Response::class, $resultResponse = $kernel->handle($request));
        static::assertStringContainsString('hello foo bar.', $resultResponse->getContent());
        static::assertStringContainsString('Exception: hello foo bar. in file', $resultResponse->getContent());
        static::assertStringContainsString('Exception->()', $resultResponse->getContent());
    }

    #[Api([
        'zh-CN:title' => '错误处理',
        'zh-CN:description' => <<<'EOT'
路由出现错误，返回错误响应。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Kernel\KernelTest::class, 'createRouterWithError')]}
```
EOT,
    ])]
    public function testRouterWillThrowError(): void
    {
        $app = new AppKernel($container = new Container(), '');
        $container->instance('app', $app);

        $request = $this->createMock(Request::class);

        $router = $this->createRouterWithError();
        $this->createOption($container, true);
        $this->createLog($container);
        $this->createRuntimeWithRender($container);

        $kernel = new Kernel1($app, $router);
        $this->assertInstanceof(IKernel::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());

        $this->assertInstanceof(Response::class, $resultResponse = $kernel->handle($request));

        static::assertStringContainsString('ErrorException: hello bar foo', $resultResponse->getContent());
        static::assertStringContainsString('ErrorException->()', $resultResponse->getContent());
    }

    #[Api([
        'zh-CN:title' => '系统中间件',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Kernel\Kernel2**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Kernel2::class)]}
```

**Tests\Kernel\Middlewares\Demo1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Middlewares\Demo1::class)]}
```
EOT,
    ])]
    public function test2(): void
    {
        $app = new AppKernel($container = new Container(), '');
        $container->instance('app', $app);

        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $router = $this->createRouter($response);
        $this->createOption($container, true);
        $this->createLog($container);
        $this->createRuntime($container);

        $kernel = new Kernel2($app, $router);
        $this->assertInstanceof(IKernel::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());
        $this->assertInstanceof(Response::class, $resultResponse = $kernel->handle($request));
        $kernel->terminate($request, $resultResponse);
        static::assertSame(['Demo1::terminate'], $GLOBALS['demo_middlewares']);
        unset($GLOBALS['demo_middlewares']);
    }

    #[Api([
        'zh-CN:title' => '系统中间件支持 handle 和 terminate',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Kernel\Kernel3**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Kernel3::class)]}
```

**Tests\Kernel\Middlewares\Demo2**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Middlewares\Demo2::class)]}
```
EOT,
    ])]
    public function test3(): void
    {
        $app = new AppKernel($container = new Container(), '');
        $container->instance('app', $app);

        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $router = $this->createRouter($response);
        $this->createOption($container, true);
        $this->createLog($container);
        $this->createRuntime($container);

        $kernel = new Kernel3($app, $router);
        $this->assertInstanceof(IKernel::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());
        $this->assertInstanceof(Response::class, $resultResponse = $kernel->handle($request));
        $kernel->terminate($request, $resultResponse);
        static::assertSame(['Demo2::handle', 'Demo2::terminate'], $GLOBALS['demo_middlewares']);
        unset($GLOBALS['demo_middlewares']);
    }

    #[Api([
        'zh-CN:title' => '系统中间件支持参数',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Kernel\Kernel4**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Kernel4::class)]}
```

**Tests\Kernel\Middlewares\Demo3**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Middlewares\Demo3::class)]}
```
EOT,
    ])]
    public function test4(): void
    {
        $app = new AppKernel($container = new Container(), '');
        $container->instance('app', $app);

        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $router = $this->createRouter($response);
        $this->createOption($container, true);
        $this->createLog($container);
        $this->createRuntime($container);

        $kernel = new Kernel4($app, $router);
        $this->assertInstanceof(IKernel::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());
        $this->assertInstanceof(Response::class, $resultResponse = $kernel->handle($request));
        $kernel->terminate($request, $resultResponse);
        static::assertSame(['Demo3::handle(arg1:5,arg2:foo)'], $GLOBALS['demo_middlewares']);
        unset($GLOBALS['demo_middlewares']);
    }

    protected function createLog(IContainer $container): void
    {
        $log = $this->createMock(ILog::class);
        $container->singleton(ILog::class, function () use ($log) {
            return $log;
        });
    }

    protected function createOption(IContainer $container, bool $debug): void
    {
        $option = $this->createMock(IOption::class);

        $option
            ->method('get')
            ->willReturnCallback(function (string $k) use ($debug) {
                $map = [
                    'debug' => $debug,
                    'environment' => 'development',
                ];

                return $map[$k];
            })
        ;

        static::assertSame($debug, $option->get('debug'));
        static::assertSame('development', $option->get('environment'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });
    }

    protected function createRuntime(IContainer $container): void
    {
        $runtime = $this->createMock(IRuntime::class);
        $container->singleton(IRuntime::class, function () use ($runtime) {
            return $runtime;
        });
    }

    protected function createRuntimeWithRender(IContainer $container): void
    {
        $runtime = new ExceptionRuntime1($container->make('app'));
        $container->singleton(IRuntime::class, function () use ($runtime) {
            return $runtime;
        });
    }

    protected function createRouter(Response $response): IRouter
    {
        $request = $this->createMock(Request::class);
        $router = $this->createMock(IRouter::class);
        $router->method('dispatch')->willReturn($response);
        static::assertSame($response, $router->dispatch($request));

        return $router;
    }

    protected function createRouterWithException(): IRouter
    {
        $this->createMock(Request::class);
        $router = $this->createMock(IRouter::class);
        $router->method('dispatch')->will(static::throwException(new \Exception('hello foo bar.')));

        return $router;
    }

    protected function createRouterWithError(): IRouter
    {
        $request = $this->createMock(Request::class);
        $router = $this->createMock(IRouter::class);
        $router->method('dispatch')->will(static::throwException(new \Error('hello bar foo.')));

        return $router;
    }
}

class Kernel1 extends Kernel
{
    protected array $bootstraps = [
        DemoBootstrapForKernel::class,
    ];
}

class Kernel2 extends Kernel
{
    protected array $middlewares = [
        Demo1::class,
    ];
}

class Kernel3 extends Kernel
{
    protected array $middlewares = [
        Demo2::class,
    ];
}

class Kernel4 extends Kernel
{
    protected array $middlewares = [
        Demo3::class.':5,foo',
    ];
}

class AppKernel extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class ExceptionRuntime1 extends Runtime
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

class DemoBootstrapForKernel
{
    public function handle(IApp $app): void
    {
        $GLOBALS['DemoBootstrapForKernel'] = true;
    }
}
