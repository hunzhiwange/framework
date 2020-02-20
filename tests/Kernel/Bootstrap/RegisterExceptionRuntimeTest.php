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

namespace Tests\Kernel\Bootstrap;

use Error;
use ErrorException;
use Exception;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\Request;
use Leevel\Http\Response;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Bootstrap\RegisterExceptionRuntime;
use Leevel\Kernel\IApp;
use Leevel\Kernel\IExceptionRuntime;
use Symfony\Component\Console\Output\ConsoleOutput;
use Tests\TestCase;

/**
 * @api(
 *     title="初始化注册异常运行时",
 *     path="architecture/bootstrap/registerexceptionruntime",
 *     description="
 * QueryPHP 在内核执行过程中会执行初始化，分为 4 个步骤，载入配置、载入语言包、注册异常运行时和遍历服务提供者注册服务。
 *
 * 内核初始化，包括 `\Leevel\Kernel\IKernel::bootstrap` 和 `\Leevel\Kernel\IKernelConsole::bootstrap` 均会执行上述 4 个步骤。
 * ",
 *     note="",
 * )
 */
class RegisterExceptionRuntimeTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    /**
     * @api(
     *     title="set_error_handler 设置错误处理函数",
     *     description="",
     *     note="",
     * )
     */
    public function testSetErrorHandle(): void
    {
        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage(
            'foo.'
        );

        $bootstrap = new RegisterExceptionRuntime();

        $container = Container::singletons();
        $app = new App4($container, $appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        $this->invokeTestMethod($bootstrap, 'setErrorHandle', [400, 'foo.']);
    }

    public function testSetErrorHandle2(): void
    {
        $bootstrap = new RegisterExceptionRuntime();

        $container = Container::singletons();
        $app = new App4($container, $appPath = __DIR__.'/app');

        $this->assertNull($this->invokeTestMethod($bootstrap, 'setErrorHandle', [0, 'foo.']));
    }

    /**
     * @api(
     *     title="set_exception_handler 设置异常处理函数",
     *     description="",
     *     note="",
     * )
     */
    public function testSetExceptionHandler(): void
    {
        $bootstrap = new RegisterExceptionRuntime();

        $container = Container::singletons();
        $app = new App4($container, $appPath = __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isConsole')->willReturn(true);
        $this->assertTrue($request->isConsole());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $runtime = $this->createMock(IExceptionRuntime::class);

        $this->assertNull($runtime->renderForConsole(new ConsoleOutput(), new Exception()));

        $container->singleton(IExceptionRuntime::class, function () use ($runtime) {
            return $runtime;
        });

        $bootstrap->handle($app, true);

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        $e = new Exception('foo.');

        $this->assertNull($this->invokeTestMethod($bootstrap, 'setExceptionHandler', [$e]));

        $error = new Error('hello world.');

        $this->assertNull($this->invokeTestMethod($bootstrap, 'setExceptionHandler', [$error]));
    }

    public function testSetExceptionHandler2(): void
    {
        $bootstrap = new RegisterExceptionRuntime();

        $container = Container::singletons();
        $app = new App4($container, $appPath = __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isConsole')->willReturn(false);
        $this->assertFalse($request->isConsole());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $e = new Exception('foo.');

        $response = $this->createMock(Response::class);
        $runtime = $this->createMock(IExceptionRuntime::class);

        $runtime->method('render')->willReturn($response);
        $this->assertSame($response, $runtime->render($request, $e));

        $container->singleton(IExceptionRuntime::class, function () use ($runtime) {
            return $runtime;
        });

        $bootstrap->handle($app, true);

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        $this->invokeTestMethod($bootstrap, 'setExceptionHandler', [$e]);

        $error = new Error('hello world.');

        $this->assertNull($this->invokeTestMethod($bootstrap, 'setExceptionHandler', [$error]));
    }

    public function testFormatErrorException(): void
    {
        $bootstrap = new RegisterExceptionRuntime();

        $container = Container::singletons();
        $app = new App4($container, $appPath = __DIR__.'/app');

        $error = ['message' => 'foo.', 'type' => 5, 'file' => 'a.txt', 'line' => 5];

        $e = $this->invokeTestMethod($bootstrap, 'formatErrorException', [$error]);

        $this->assertInstanceof(ErrorException::class, $e);
        $this->assertSame('foo.', $e->getMessage());
    }
}

class App4 extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}
