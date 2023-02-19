<?php

declare(strict_types=1);

namespace Tests\Kernel\Bootstrap;

use Error;
use Exception;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\Request;
use Leevel\Http\Response;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Bootstrap\RegisterExceptionRuntime;
use Leevel\Kernel\Exceptions\IRuntime;
use Leevel\Kernel\IApp;
use Leevel\Option\IOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="初始化注册异常运行时",
 *     path="architecture/kernel/bootstrap/registerexceptionruntime",
 *     zh-CN:description="
 * QueryPHP 在内核执行过程中会执行初始化，分为 4 个步骤，载入配置、载入语言包、注册异常运行时和遍历服务提供者注册服务。
 *
 * 内核初始化，包括 `\Leevel\Kernel\IKernel::bootstrap` 和 `\Leevel\Kernel\IKernelConsole::bootstrap` 均会执行上述 4 个步骤。
 * ",
 *     zh-CN:note="",
 * )
 */
final class RegisterExceptionRuntimeTest extends TestCase
{
    protected int $oldErrorReporting;
    protected string $oldDisplayErrors;

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
     *     zh-CN:title="set_error_handler 设置错误处理函数",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        static::assertNull($this->invokeTestMethod($bootstrap, 'setErrorHandle', [0, 'foo.']));
    }

    /**
     * @api(
     *     zh-CN:title="set_exception_handler 设置异常处理函数",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSetExceptionHandler(): void
    {
        $this->backupSystemEnvironment();
        $bootstrap = new RegisterExceptionRuntime1();

        $container = Container::singletons();
        $app = new App4($container, $appPath = __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isConsole')->willReturn(true);
        static::assertTrue($request->isConsole());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $runtime = $this->createMock(IRuntime::class);

        static::assertNull($runtime->renderForConsole(new ConsoleOutput(), new \Exception()));

        $container->singleton(IRuntime::class, function () use ($runtime) {
            return $runtime;
        });

        $option = $this->createMock(IOption::class);
        $option->method('get')->willReturn('production');
        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $bootstrap->handle($app);

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        $e = new \Exception('foo.');

        static::assertNull($this->invokeTestMethod($bootstrap, 'setExceptionHandler', [$e]));

        $error = new \Error('hello world.');

        static::assertNull($this->invokeTestMethod($bootstrap, 'setExceptionHandler', [$error]));
        $this->restoreSystemEnvironment();
    }

    /**
     * @api(
     *     zh-CN:title="register_shutdown_function 设置请求关闭处理函数",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testRegisterShutdownFunction(): void
    {
        $bootstrap = new RegisterExceptionRuntime2();

        $container = Container::singletons();
        $app = new App4($container, $appPath = __DIR__.'/app');

        set_error_handler('var_dump', 0);
        trigger_error('hello error');
        restore_error_handler();

        $this->setTestProperty($bootstrap, 'app', $app);
        static::assertNull($this->invokeTestMethod($bootstrap, 'registerShutdownFunction'));
        static::assertSame($GLOBALS['testRegisterShutdownFunction'], 'hello error');
        unset($GLOBALS['testRegisterShutdownFunction']);
    }

    public function testSetExceptionHandler2(): void
    {
        $this->backupSystemEnvironment();
        $bootstrap = new RegisterExceptionRuntime1();

        $container = Container::singletons();
        $app = new App4($container, $appPath = __DIR__.'/app');

        $request = $this->createMock(Request::class);

        $request->method('isConsole')->willReturn(false);
        static::assertFalse($request->isConsole());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $e = new \Exception('foo.');

        $response = $this->createMock(Response::class);
        $runtime = $this->createMock(IRuntime::class);

        $runtime->method('render')->willReturn($response);
        static::assertSame($response, $runtime->render($request, $e));

        $container->singleton(IRuntime::class, function () use ($runtime) {
            return $runtime;
        });

        $option = $this->createMock(IOption::class);
        $option->method('get')->willReturn('production');
        $container->singleton('option', function () use ($option) {
            return $option;
        });
        $bootstrap->handle($app);

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        $this->invokeTestMethod($bootstrap, 'setExceptionHandler', [$e]);

        $error = new \Error('hello world.');

        static::assertNull($this->invokeTestMethod($bootstrap, 'setExceptionHandler', [$error]));
        $this->restoreSystemEnvironment();
    }

    public function testFormatErrorException(): void
    {
        $bootstrap = new RegisterExceptionRuntime();

        $container = Container::singletons();
        $app = new App4($container, $appPath = __DIR__.'/app');

        $error = ['message' => 'foo.', 'type' => 5, 'file' => 'a.txt', 'line' => 5];

        $e = $this->invokeTestMethod($bootstrap, 'formatErrorException', [$error]);

        $this->assertInstanceof(\ErrorException::class, $e);
        static::assertSame('foo.', $e->getMessage());
    }

    protected function backupSystemEnvironment(): void
    {
        $this->oldErrorReporting = error_reporting();
        $this->oldDisplayErrors = \ini_get('display_errors');
    }

    protected function restoreSystemEnvironment(): void
    {
        restore_error_handler();
        restore_exception_handler();
        error_reporting($this->oldErrorReporting);
        ini_set('display_errors', $this->oldDisplayErrors);
    }
}

class App4 extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class RegisterExceptionRuntime1 extends RegisterExceptionRuntime
{
    public function registerShutdownFunction(): void
    {
    }
}

class RegisterExceptionRuntime2 extends RegisterExceptionRuntime
{
    public function setExceptionHandler(\Throwable $e): void
    {
        $GLOBALS['testRegisterShutdownFunction'] = $e->getMessage();
    }
}
