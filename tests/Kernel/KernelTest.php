<?php

declare(strict_types=1);

namespace Tests\Kernel;

use Error;
use Exception;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\JsonResponse;
use Leevel\Http\Request;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Exception\HttpException;
use Leevel\Kernel\ExceptionRuntime;
use Leevel\Kernel\IApp;
use Leevel\Kernel\IExceptionRuntime;
use Leevel\Kernel\IKernel;
use Leevel\Kernel\Kernel;
use Leevel\Log\ILog;
use Leevel\Option\IOption;
use Leevel\Router\IRouter;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="内核",
 *     path="architecture/kernel",
 *     zh-CN:description="
 * QueryPHP 流程为入口接受 HTTP 请求，经过内核 kernel 传入请求，经过路由解析调用控制器执行业务，最后返回响应结果。
 *
 * 入口文件 `www/index.php`
 *
 * ``` php
 * {[file_get_contents('www/index.php')]}
 * ```
 *
 * 内核通过 \Leevel\Kernel\Kernel 的 handle 方法来实现请求。
 *
 * **handle 原型**
 *
 * ``` php
 * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Kernel\Kernel::class, 'handle', 'define')]}
 * ```
 * ",
 *     zh-CN:note="
 * 内核设计为可替代，只需要实现 `\Leevel\Kernel\IKernel` 即可，然后在入口文件替换即可。
 * ",
 * )
 */
class KernelTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @api(
     *     zh-CN:title="基本使用",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Kernel\Kernel1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Kernel1::class)]}
     * ```
     *
     * **Tests\Kernel\DemoBootstrapForKernel**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\DemoBootstrapForKernel::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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
        $this->assertTrue($GLOBALS['DemoBootstrapForKernel']);
        unset($GLOBALS['DemoBootstrapForKernel']);
    }

    public function baseUseProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @api(
     *     zh-CN:title="JSON 响应例子",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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
        $this->assertSame('{"foo":"bar"}', $resultResponse->getContent());
    }

    /**
     * @api(
     *     zh-CN:title="异常处理",
     *     zh-CN:description="
     * 路由抛出异常，返回异常响应。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Kernel\KernelTest::class, 'createRouterWithException')]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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
        $this->assertStringContainsString('hello foo bar.', $resultResponse->getContent());

        $this->assertStringContainsString('<span>hello foo bar.</span>', $resultResponse->getContent());
        $this->assertStringContainsString('<span class="exc-title-primary">Exception</span>', $resultResponse->getContent());
    }

    /**
     * @api(
     *     zh-CN:title="错误处理",
     *     zh-CN:description="
     * 路由出现错误，返回错误响应。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Kernel\KernelTest::class, 'createRouterWithError')]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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

        $this->assertStringContainsString('<span>hello bar foo.</span>', $resultResponse->getContent());
        $this->assertStringContainsString('<span class="exc-title-primary">ErrorException</span>', $resultResponse->getContent());
    }

    protected function createLog(IContainer $container): void
    {
        $log = $this->createMock(ILog::class);
        $log->method('all')->willReturn([]);
        $this->assertSame([], $log->all());

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
                    'debug'       => $debug,
                    'environment' => 'development',
                ];

                return $map[$k];
            });

        $this->assertSame($debug, $option->get('debug'));
        $this->assertSame('development', $option->get('environment'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });
    }

    protected function createRuntime(IContainer $container): void
    {
        $runtime = $this->createMock(IExceptionRuntime::class);
        $container->singleton(IExceptionRuntime::class, function () use ($runtime) {
            return $runtime;
        });
    }

    protected function createRuntimeWithRender(IContainer $container): void
    {
        $runtime = new ExceptionRuntime1($container->make('app'));
        $container->singleton(IExceptionRuntime::class, function () use ($runtime) {
            return $runtime;
        });
    }

    protected function createRouter(Response $response): IRouter
    {
        $request = $this->createMock(Request::class);
        $router = $this->createMock(IRouter::class);
        $router->method('dispatch')->willReturn($response);
        $this->assertSame($response, $router->dispatch($request));

        return $router;
    }

    protected function createRouterWithException(): IRouter
    {
        $request = $this->createMock(Request::class);
        $router = $this->createMock(IRouter::class);
        $router->method('dispatch')->will($this->throwException(new Exception('hello foo bar.')));

        return $router;
    }

    protected function createRouterWithError(): IRouter
    {
        $request = $this->createMock(Request::class);
        $router = $this->createMock(IRouter::class);
        $router->method('dispatch')->will($this->throwException(new Error('hello bar foo.')));

        return $router;
    }
}

class Kernel1 extends Kernel
{
    protected array $bootstraps = [
        DemoBootstrapForKernel::class,
    ];
}

class AppKernel extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class ExceptionRuntime1 extends ExceptionRuntime
{
    public function getHttpExceptionView(HttpException $e): string
    {
        return '';
    }

    public function getDefaultHttpExceptionView(): string
    {
        return '';
    }
}

class DemoBootstrapForKernel
{
    public function handle(IApp $app): void
    {
        $GLOBALS['DemoBootstrapForKernel'] = true;
    }
}
