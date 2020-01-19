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
use Leevel\Kernel\Bootstrap\RegisterRuntime;
use Leevel\Kernel\IApp;
use Leevel\Kernel\IRuntime;
use Symfony\Component\Console\Output\ConsoleOutput;
use Tests\TestCase;

class RegisterRuntimeTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testSetErrorHandle(): void
    {
        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage(
            'foo.'
        );

        $bootstrap = new RegisterRuntime();

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
        $bootstrap = new RegisterRuntime();

        $container = Container::singletons();
        $app = new App4($container, $appPath = __DIR__.'/app');

        $this->assertNull($this->invokeTestMethod($bootstrap, 'setErrorHandle', [0, 'foo.']));
    }

    public function testSetExceptionHandler(): void
    {
        $bootstrap = new RegisterRuntime();

        $container = Container::singletons();
        $app = new App4($container, $appPath = __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isCli')->willReturn(true);
        $this->assertTrue($request->isCli());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $runtime = $this->createMock(IRuntime::class);

        $this->assertNull($runtime->renderForConsole(new ConsoleOutput(), new Exception()));

        $container->singleton(IRuntime::class, function () use ($runtime) {
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
        $bootstrap = new RegisterRuntime();

        $container = Container::singletons();
        $app = new App4($container, $appPath = __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isCli')->willReturn(false);
        $this->assertFalse($request->isCli());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $e = new Exception('foo.');

        $response = $this->createMock(Response::class);
        $runtime = $this->createMock(IRuntime::class);

        $runtime->method('render')->willReturn($response);
        $this->assertSame($response, $runtime->render($request, $e));

        $container->singleton(IRuntime::class, function () use ($runtime) {
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
        $bootstrap = new RegisterRuntime();

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
