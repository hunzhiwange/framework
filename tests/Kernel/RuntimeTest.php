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

namespace Tests\Kernel;

use Exception;
use Leevel\Database\Ddd\EntityNotFoundException;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Http\Response;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Exception\InternalServerErrorHttpException;
use Leevel\Kernel\Exception\MethodNotAllowedHttpException;
use Leevel\Kernel\Exception\NotFoundHttpException;
use Leevel\Kernel\Runtime;
use Leevel\Log\ILog;
use Leevel\Option\Option;
use Tests\TestCase;

/**
 * runtime test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.21
 *
 * @version 1.0
 */
class RuntimeTest extends TestCase
{
    public function testBaseUse(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);

        $option = new Option([
            'app' => [
                '_composer' => [
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

        $this->assertNull($log->error('hello world', []));

        $container->singleton(ILog::class, function () use ($log) {
            return $log;
        });

        $runtime = new Runtime11($app);

        $e = new Exception1('hello world');

        $this->assertNull($runtime->report($e));
    }

    public function testExceptionItSelfWithReport(): void
    {
        $app = new AppRuntime(new Container(), __DIR__.'/app');

        $runtime = new Runtime11($app);

        $e = new Exception2('hello world');

        $this->assertArrayNotHasKey('testExceptionItSelfWithReport', $_SERVER);

        $this->assertNull($runtime->report($e));

        $this->assertSame(1, $_SERVER['testExceptionItSelfWithReport']);

        unset($_SERVER['testExceptionItSelfWithReport']);
    }

    public function testRender(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('isAcceptJson')->willReturn(false);
        $this->assertFalse($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug'       => true,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime11($app);

        $e = new Exception1('hello world');

        $this->assertInstanceof(IResponse::class, $resultResponse = $runtime->render($request, $e));
        $this->assertStringContainsString('Tests\\Kernel\\Exception1: hello world in file', $resultResponse->getContent());
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    public function testRenderWithCustomRenderMethod(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('isAcceptJson')->willReturn(false);
        $this->assertFalse($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug'       => true,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime11($app);

        $e = new Exception3('hello world');

        $this->assertInstanceof(IResponse::class, $resultResponse = $runtime->render($request, $e));

        $this->assertSame('hello world', $resultResponse->getContent());
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    public function testRenderWithCustomRenderMethod2(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('isAcceptJson')->willReturn(false);
        $this->assertFalse($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug'       => true,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime11($app);

        $e = new Exception4('hello world');

        $this->assertInstanceof(IResponse::class, $resultResponse = $runtime->render($request, $e));

        $this->assertSame('foo bar', $resultResponse->getContent());
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    public function testRenderWithCustomRenderMethodToJson(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('isAcceptJson')->willReturn(true);
        $this->assertTrue($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug'       => true,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime11($app);

        $e = new Exception5('hello world');

        $this->assertInstanceof(IResponse::class, $resultResponse = $runtime->render($request, $e));

        $this->assertSame('{"foo":"bar"}', $resultResponse->getContent());
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    public function testRenderToJson(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('isAcceptJson')->willReturn(true);
        $this->assertTrue($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug'       => true,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime11($app);

        $e = new Exception1('hello world');

        $this->assertInstanceof(IResponse::class, $resultResponse = $runtime->render($request, $e));

        $this->assertIsArray($content = json_decode($resultResponse->getContent(), true));
        $this->assertArrayHasKey('error', $content);
        $this->assertSame('Tests\\Kernel\\Exception1', $content['error']['type']);
        $this->assertSame('hello world', $content['error']['message']);
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    public function testRendorWithHttpExceptionView(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $option = new Option([
            'app' => [
                'debug'       => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime22($app);

        $e = new Exception6('hello world');

        $this->assertInstanceof(IResponse::class, $resultResponse = $runtime->rendorWithHttpExceptionView($e));

        $content = $resultResponse->getContent();

        $this->assertStringContainsString('<div id="status-code">500</div>', $content);
        $this->assertStringContainsString('<p id="title">服务器内部错误</p>', $content);
        $this->assertStringContainsString('<p id="sub-title">0 服务器遇到错误，无法完成请求</p>', $content);
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    public function testRendorWithHttpExceptionViewFor404(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $option = new Option([
            'app' => [
                'debug'       => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime22($app);

        $e = new Exception7('hello world');

        $this->assertInstanceof(IResponse::class, $resultResponse = $runtime->rendorWithHttpExceptionView($e));

        $content = $resultResponse->getContent();

        $this->assertStringContainsString('<div id="status-code">404</div>', $content);
        $this->assertStringContainsString('<p id="title">页面未找到</p>', $content);
        $this->assertStringContainsString('<p id="sub-title">0 用户发出的请求针对的是不存在的页面</p>', $content);
        $this->assertSame(404, $resultResponse->getStatusCode());
    }

    public function testRendorWithHttpExceptionViewButNotFoundView(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $option = new Option([
            'app' => [
                'debug'       => true,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime22($app);

        $e = new Exception8('hello world');

        $this->assertInstanceof(IResponse::class, $resultResponse = $runtime->rendorWithHttpExceptionView($e));

        $content = $resultResponse->getContent();

        $this->assertStringContainsString('Tests\\Kernel\\Exception8: hello world', $content);
        $this->assertSame(405, $resultResponse->getStatusCode());
    }

    public function testRenderWithDebugIsOff(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('isAcceptJson')->willReturn(false);
        $this->assertFalse($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug'       => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime22($app);

        $e = new Exception1('hello world');

        $this->assertInstanceof(IResponse::class, $resultResponse = $runtime->render($request, $e));

        $content = $resultResponse->getContent();

        $this->assertStringContainsString('<div id="status-code">500</div>', $content);
        $this->assertStringContainsString('<p id="title">服务器内部错误</p>', $content);
        $this->assertStringContainsString('<p id="sub-title">0 服务器遇到错误，无法完成请求</p>', $content);
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    public function testRenderWithDebugIsOn(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('isAcceptJson')->willReturn(false);
        $this->assertFalse($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug'       => true,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime22($app);

        $e = new Exception1('hello world');

        $this->assertInstanceof(IResponse::class, $resultResponse = $runtime->render($request, $e));

        $content = $resultResponse->getContent();

        $this->assertStringContainsString('Tests\\Kernel\\Exception1: hello world', $content);
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    public function testRendorWithHttpExceptionViewButNotFoundViewAndWithDefaultView(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $option = new Option([
            'app' => [
                'debug'       => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime3($app);

        $e = new Exception8('hello world');

        $this->assertInstanceof(IResponse::class, $resultResponse = $runtime->rendorWithHttpExceptionView($e));

        $content = $resultResponse->getContent();

        $this->assertStringContainsString('<div id="status-code">405</div>', $content);
        $this->assertStringContainsString('Tests\\Kernel\\Exception8<div class="file">#FILE', $content);
        $this->assertStringContainsString('<p id="sub-title">0 hello world</p>', $content);
        $this->assertSame(405, $resultResponse->getStatusCode());
    }

    public function testRendorWithHttpExceptionViewButNotFoundViewAndWithDefaultViewButNotStill(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            sprintf('Exception file %s is not extis.', __DIR__.'/assert/notFoundDefault.php')
        );

        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $option = new Option([
            'app' => [
                'debug'       => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime4($app);

        $e = new Exception8('hello world');

        $runtime->rendorWithHttpExceptionView($e);
    }

    public function testRenderForEntityNotFoundException(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('isAcceptJson')->willReturn(false);
        $this->assertFalse($request->isAcceptJson());

        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $option = new Option([
            'app' => [
                'debug'       => false,
                'environment' => 'development',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $runtime = new Runtime22($app);

        $e = new EntityNotFoundException('hello world');

        $this->assertInstanceof(IResponse::class, $resultResponse = $runtime->render($request, $e));

        $content = $resultResponse->getContent();

        $this->assertStringContainsString('<div id="status-code">404</div>', $content);
        $this->assertStringContainsString('<p id="title">页面未找到</p>', $content);
        $this->assertStringContainsString('<p id="sub-title">0 用户发出的请求针对的是不存在的页面</p>', $content);
        $this->assertSame(404, $resultResponse->getStatusCode());
    }
}

class AppRuntime extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class Runtime11 extends Runtime
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

class Runtime22 extends Runtime
{
    public function getHttpExceptionView(Exception $e): string
    {
        return __DIR__.'/assert/'.$e->getStatusCode().'.php';
    }

    public function getDefaultHttpExceptionView(): string
    {
        return '';
    }
}

class Runtime3 extends Runtime
{
    public function getHttpExceptionView(Exception $e): string
    {
        return __DIR__.'/assert/notFound.php';
    }

    public function getDefaultHttpExceptionView(): string
    {
        return __DIR__.'/assert/default.php';
    }
}

class Runtime4 extends Runtime
{
    public function getHttpExceptionView(Exception $e): string
    {
        return __DIR__.'/assert/notFound.php';
    }

    public function getDefaultHttpExceptionView(): string
    {
        return __DIR__.'/assert/notFoundDefault.php';
    }
}

class Exception1 extends Exception
{
}

class Exception2 extends Exception
{
    public function report()
    {
        $_SERVER['testExceptionItSelfWithReport'] = 1;
    }
}

class Exception3 extends Exception
{
    public function render(IRequest $request, Exception $e): string
    {
        return 'hello world';
    }
}

class Exception4 extends Exception
{
    public function render(IRequest $request, Exception $e): Response
    {
        return new Response('foo bar', 500);
    }
}

class Exception5 extends Exception
{
    public function render(IRequest $request, Exception $e): array
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
}
