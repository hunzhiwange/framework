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

namespace Tests\Leevel\Bootstrap;

use Error;
use ErrorException;
use Exception;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Kernel\IRuntime;
use Leevel\Leevel\Bootstrap\RegisterRuntime;
use Leevel\Leevel\Project as Projects;
use Symfony\Component\Console\Output\ConsoleOutput;
use Tests\TestCase;

/**
 * registerRuntime test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.21
 *
 * @version 1.0
 */
class RegisterRuntimeTest extends TestCase
{
    public function testSetErrorHandle()
    {
        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage(
            'foo.'
        );

        $bootstrap = new RegisterRuntime();

        $project = new Project4($appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $project);
        $this->assertInstanceof(Container::class, $project);

        $this->invokeTestMethod($bootstrap, 'setErrorHandle', [400, 'foo.']);
    }

    public function testSetErrorHandle2()
    {
        $bootstrap = new RegisterRuntime();

        $project = new Project4($appPath = __DIR__.'/app');

        $this->assertNull($this->invokeTestMethod($bootstrap, 'setErrorHandle', [0, 'foo.']));
    }

    public function testSetExceptionHandler()
    {
        $bootstrap = new RegisterRuntime();

        $project = new Project4($appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('isCli')->willReturn(true);
        $this->assertTrue($request->isCli());

        $project->singleton('request', function () use ($request) {
            return $request;
        });

        $runtime = $this->createMock(IRuntime::class);

        $runtime->method('renderForConsole')->willReturn(null);
        $this->assertNull($runtime->renderForConsole(new ConsoleOutput(), new Exception()));

        $project->singleton(IRuntime::class, function () use ($runtime) {
            return $runtime;
        });

        $bootstrap->handle($project, true);

        $this->assertInstanceof(IContainer::class, $project);
        $this->assertInstanceof(Container::class, $project);

        $e = new Exception('foo.');

        $this->assertNull($this->invokeTestMethod($bootstrap, 'setExceptionHandler', [$e]));

        $error = new Error('hello world.');

        $this->assertNull($this->invokeTestMethod($bootstrap, 'setExceptionHandler', [$error]));
    }

    public function testSetExceptionHandler2()
    {
        $bootstrap = new RegisterRuntime();

        $project = new Project4($appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('isCli')->willReturn(false);
        $this->assertFalse($request->isCli());

        $project->singleton('request', function () use ($request) {
            return $request;
        });

        $e = new Exception('foo.');

        $response = $this->createMock(IResponse::class);

        $runtime = $this->createMock(IRuntime::class);

        $runtime->method('render')->willReturn($response);
        $this->assertSame($response, $runtime->render($request, $e));

        $project->singleton(IRuntime::class, function () use ($runtime) {
            return $runtime;
        });

        $bootstrap->handle($project, true);

        $this->assertInstanceof(IContainer::class, $project);
        $this->assertInstanceof(Container::class, $project);

        $this->invokeTestMethod($bootstrap, 'setExceptionHandler', [$e]);

        $error = new Error('hello world.');

        $this->assertNull($this->invokeTestMethod($bootstrap, 'setExceptionHandler', [$error]));
    }

    public function testFormatErrorException()
    {
        $bootstrap = new RegisterRuntime();

        $project = new Project4($appPath = __DIR__.'/app');

        $error = ['message' => 'foo.', 'type' => 5, 'file' => 'a.txt', 'line' => 5];

        $e = $this->invokeTestMethod($bootstrap, 'formatErrorException', [$error]);

        $this->assertInstanceof(ErrorException::class, $e);
        $this->assertSame('foo.', $e->getMessage());
    }
}

class Project4 extends Projects
{
    protected function registerBaseProvider(): void
    {
    }
}
