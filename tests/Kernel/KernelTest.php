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

namespace Tests\Kernel;

use Error;
use Exception;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\ApiResponse;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Http\JsonResponse;
use Leevel\Http\Response;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\IApp;
use Leevel\Kernel\IKernel;
use Leevel\Kernel\IRuntime;
use Leevel\Kernel\Kernel;
use Leevel\Kernel\Runtime;
use Leevel\Log\ILog;
use Leevel\Option\IOption;
use Leevel\Router\IRouter;
use Tests\TestCase;

/**
 * kernel test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.23
 *
 * @version 1.0
 */
class KernelTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param bool $debug
     */
    public function testBaseUse(bool $debug)
    {
        $app = new AppKernel($container = new Container(), '');
        $container->instance('app', $app);

        $request = $this->createMock(IRequest::class);
        $response = $this->createMock(IResponse::class);

        $router = $this->createRouter($response);
        $this->createOption($container, $debug);
        $this->createLog($container);
        $this->createRuntime($container);

        $kernel = new Kernel1($app, $router);
        $this->assertInstanceof(IKernel::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());

        $this->assertInstanceof(IResponse::class, $resultResponse = $kernel->handle($request));
    }

    public function baseUseProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    public function testWithResponseIsJson(): void
    {
        $app = new AppKernel($container = new Container(), '');
        $container->instance('app', $app);

        $request = $this->createMock(IRequest::class);
        $response = new JsonResponse(['foo' => 'bar']);

        $router = $this->createRouter($response);
        $this->createOption($container, true);
        $this->createLog($container);
        $this->createRuntime($container);

        $kernel = new Kernel1($app, $router);
        $this->assertInstanceof(IKernel::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());

        $this->assertInstanceof(IResponse::class, $resultResponse = $kernel->handle($request));
        $this->assertSame('{"foo":"bar"}', $resultResponse->getContent());
    }

    public function testWithResponseIsJson2(): void
    {
        $app = new AppKernel($container = new Container(), '');
        $container->instance('app', $app);

        $request = $this->createMock(IRequest::class);
        $response = (new ApiResponse())->ok(['foo' => 'bar']);

        $router = $this->createRouter($response);
        $this->createOption($container, true);
        $this->createLog($container);
        $this->createRuntime($container);

        $kernel = new Kernel1($app, $router);
        $this->assertInstanceof(IKernel::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());

        $this->assertInstanceof(IResponse::class, $resultResponse = $kernel->handle($request));
        $this->assertSame('{"foo":"bar"}', $resultResponse->getContent());
    }

    public function testWithResponseIsJson3(): void
    {
        $app = new AppKernel($container = new Container(), '');
        $container->instance('app', $app);

        $request = $this->createMock(IRequest::class);
        $response = new Response(['foo' => 'bar']);

        $router = $this->createRouter($response);
        $this->createOption($container, true);
        $this->createLog($container);
        $this->createRuntime($container);

        $kernel = new Kernel1($app, $router);
        $this->assertInstanceof(IKernel::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());

        $this->assertInstanceof(IResponse::class, $resultResponse = $kernel->handle($request));
        $this->assertSame('{"foo":"bar"}', $resultResponse->getContent());
    }

    public function testRouterWillThrowException(): void
    {
        $app = new AppKernel($container = new Container(), '');
        $container->instance('app', $app);

        $request = $this->createMock(IRequest::class);

        $router = $this->createRouterWithException();
        $this->createOption($container, true);
        $this->createLog($container);
        $this->createRuntimeWithRender($container);

        $kernel = new Kernel1($app, $router);
        $this->assertInstanceof(IKernel::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());

        $this->assertInstanceof(IResponse::class, $resultResponse = $kernel->handle($request));
        $this->assertStringContainsString('hello foo bar.', $resultResponse->getContent());

        $this->assertStringContainsString('<span>hello foo bar.</span>', $resultResponse->getContent());
        $this->assertStringContainsString('<span class="exc-title-primary">Exception</span>', $resultResponse->getContent());
    }

    public function testRouterWillThrowError(): void
    {
        $app = new AppKernel($container = new Container(), '');
        $container->instance('app', $app);

        $request = $this->createMock(IRequest::class);

        $router = $this->createRouterWithError();
        $this->createOption($container, true);
        $this->createLog($container);
        $this->createRuntimeWithRender($container);

        $kernel = new Kernel1($app, $router);
        $this->assertInstanceof(IKernel::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());

        $this->assertInstanceof(IResponse::class, $resultResponse = $kernel->handle($request));

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
        $runtime = $this->createMock(IRuntime::class);

        $container->singleton(IRuntime::class, function () use ($runtime) {
            return $runtime;
        });
    }

    protected function createRuntimeWithRender(IContainer $container): void
    {
        $runtime = new Runtime1($container->make('app'));

        $container->singleton(IRuntime::class, function () use ($runtime) {
            return $runtime;
        });
    }

    protected function createRouter(IResponse $response): IRouter
    {
        $request = $this->createMock(IRequest::class);
        $router = $this->createMock(IRouter::class);
        $router->method('dispatch')->willReturn($response);
        $this->assertSame($response, $router->dispatch($request));

        return $router;
    }

    protected function createRouterWithException(): IRouter
    {
        $request = $this->createMock(IRequest::class);
        $router = $this->createMock(IRouter::class);
        $router->method('dispatch')->will($this->throwException(new Exception('hello foo bar.')));

        return $router;
    }

    protected function createRouterWithError(): IRouter
    {
        $request = $this->createMock(IRequest::class);
        $router = $this->createMock(IRouter::class);
        $router->method('dispatch')->will($this->throwException(new Error('hello bar foo.')));

        return $router;
    }
}

class Kernel1 extends Kernel
{
    public function bootstrap(): void
    {
    }
}

class AppKernel extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class Runtime1 extends Runtime
{
    public function getHttpExceptionView(Exception $e): string
    {
        return '';
    }

    public function getDefaultHttpExceptionView(): string
    {
        return '';
    }
}
