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
use Leevel\Http\Request;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Exception\HttpException;
use Leevel\Kernel\Exception\InternalServerErrorHttpException;
use Leevel\Kernel\Exception\MethodNotAllowedHttpException;
use Leevel\Kernel\Exception\NotFoundHttpException;
use Leevel\Kernel\ExceptionRuntime;
use Leevel\Log\ILog;
use Leevel\Option\Option;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @api(
 *     title="异常运行时",
 *     path="architecture/kernel/exceptionruntime",
 *     description="
 * QueryPHP 系统发生的异常统一由异常运行时进行管理，处理异常上报和返回异常响应。
 *
 * **异常运行时接口**
 *
 * ``` php
 * {[file_get_contents('vendor/hunzhiwange/framework/src/Leevel/Kernel/IExceptionRuntime.php')]}
 * ```
 *
 * **默认异常运行时提供两个抽象方法**
 *
 * **getHttpExceptionView 原型**
 *
 * ``` php
 * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Kernel\ExceptionRuntime::class, 'getHttpExceptionView', 'define')]}
 * ```
 *
 * **getDefaultHttpExceptionView 原型**
 *
 * ``` php
 * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Kernel\ExceptionRuntime::class, 'getDefaultHttpExceptionView', 'define')]}
 * ```
 *
 * 只需要实现，即可轻松接入，例如应用中的 `\Common\App\ExceptionRuntime` 实现。
 *
 * ``` php
 * {[file_get_contents('common/App/ExceptionRuntime.php')]}
 * ```
 * ",
 *     note="
 * 异常运行时设计为可替代，只需要实现 `\Leevel\Kernel\异常运行时接口` 即可，然后在入口文件替换即可。
 * ",
 * )
 */
class ExceptionRuntimeTest extends TestCase
{
    /**
     * @api(
     *     title="基本使用",
     *     description="
     * **fixture 定义**
     *
     * **Tests\Kernel\AppRuntime**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\AppRuntime::class)]}
     * ```
     *
     * **Tests\Kernel\Runtime11**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Runtime11::class)]}
     * ```
     *
     * **Tests\Kernel\Exception1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception1::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

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

        $this->assertNull($log->error('hello world', []));

        $container->singleton(ILog::class, function () use ($log) {
            return $log;
        });

        $runtime = new Runtime11($app);

        $e = new Exception1('hello world');

        $this->assertNull($runtime->report($e));
    }

    /**
     * @api(
     *     title="report 自定义异常上报",
     *     description="
     * 异常提供 `report` 方法即实现自定义异常上报。
     *
     * **fixture 定义**
     *
     * **Tests\Kernel\Exception2**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception2::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="render 开启调试模式的异常渲染",
     *     description="",
     *     note="",
     * )
     */
    public function testRender(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(Request::class);

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

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));
        $this->assertStringContainsString('Tests\\Kernel\\Exception1: hello world in file', $resultResponse->getContent());
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    /**
     * @api(
     *     title="render 自定义异常渲染",
     *     description="
     * 异常提供 `render` 方法即实现自定义异常渲染。
     *
     * **fixture 定义**
     *
     * **Tests\Kernel\Exception3**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception3::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testRenderWithCustomRenderMethod(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(Request::class);

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

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        $this->assertSame('hello world', $resultResponse->getContent());
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    /**
     * @api(
     *     title="render 自定义异常渲染直接返回响应对象",
     *     description="
     * 异常提供 `render` 方法即实现自定义异常渲染。
     *
     * **fixture 定义**
     *
     * **Tests\Kernel\Exception4**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception4::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testRenderWithCustomRenderMethod2(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(Request::class);

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

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        $this->assertSame('foo bar', $resultResponse->getContent());
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    /**
     * @api(
     *     title="render 异常渲染直接返回 JSON 数据",
     *     description="",
     *     note="",
     * )
     */
    public function testRenderToJson(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(Request::class);

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

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        $this->assertIsArray($content = json_decode($resultResponse->getContent(), true));
        $this->assertArrayHasKey('error', $content);
        $this->assertSame('Tests\\Kernel\\Exception1', $content['error']['type']);
        $this->assertSame('hello world', $content['error']['message']);
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    /**
     * @api(
     *     title="render 自定义异常渲染直接返回支持转 JSON 响应的数据",
     *     description="
     * 异常提供 `render` 方法即实现自定义异常渲染。
     *
     * **fixture 定义**
     *
     * **Tests\Kernel\Exception5**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception5::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testRenderWithCustomRenderMethodToJson(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(Request::class);

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

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        $this->assertSame('{"foo":"bar"}', $resultResponse->getContent());
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    /**
     * @api(
     *     title="render HTTP 500 异常响应渲染",
     *     description="
     * 异常提供 `render` 方法即实现自定义异常渲染。
     *
     * **fixture 定义**
     *
     * **Tests\Kernel\Runtime22**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Runtime22::class)]}
     * ```
     *
     * **Tests\Kernel\Exception6**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception6::class)]}
     * ```
     *
     * **异常模板 tests/Kernel/assert/layout.php **
     *
     * ``` php
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/assert/layout.php')]}
     * ```
     *
     * **异常模板 tests/Kernel/assert/500.php **
     *
     * ``` php
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/assert/500.php')]}
     * ```
     * ",
     *     note="",
     * )
     */
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

        $this->assertInstanceof(Response::class, $resultResponse = $this->invokeTestMethod($runtime, 'rendorWithHttpExceptionView', [$e]));

        $content = $resultResponse->getContent();

        $this->assertStringContainsString('<div id="status-code">500</div>', $content);
        $this->assertStringContainsString('<p id="title">服务器内部错误</p>', $content);
        $this->assertStringContainsString('<p id="sub-title">0 服务器遇到错误，无法完成请求</p>', $content);
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    /**
     * @api(
     *     title="render HTTP 异常响应渲染使用默认异常模板的例子",
     *     description="
     * 异常提供 `render` 方法即实现自定义异常渲染。
     *
     * **fixture 定义**
     *
     * **Tests\Kernel\Exception7**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception7::class)]}
     * ```
     *
     * **异常模板 tests/Kernel/assert/404.php **
     *
     * ``` php
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/assert/404.php')]}
     * ```
     * ",
     *     note="",
     * )
     */
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

        $this->assertInstanceof(Response::class, $resultResponse = $this->invokeTestMethod($runtime, 'rendorWithHttpExceptionView', [$e]));

        $content = $resultResponse->getContent();

        $this->assertStringContainsString('<div id="status-code">404</div>', $content);
        $this->assertStringContainsString('<p id="title">页面未找到</p>', $content);
        $this->assertStringContainsString('<p id="sub-title">0 用户发出的请求针对的是不存在的页面</p>', $content);
        $this->assertSame(404, $resultResponse->getStatusCode());
    }

    /**
     * @api(
     *     title="render HTTP 异常响应渲染",
     *     description="
     * 异常提供 `render` 方法即实现自定义异常渲染。
     *
     * **fixture 定义**
     *
     * **Tests\Kernel\Runtime3**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Runtime3::class)]}
     * ```
     *
     * **Tests\Kernel\Exception8**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Exception8::class)]}
     * ```
     *
     * **异常模板 tests/Kernel/assert/default.php **
     *
     * ``` php
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/assert/default.php')]}
     * ```
     * ",
     *     note="",
     * )
     */
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

        $this->assertInstanceof(Response::class, $resultResponse = $this->invokeTestMethod($runtime, 'rendorWithHttpExceptionView', [$e]));

        $content = $resultResponse->getContent();

        $this->assertStringContainsString('<div id="status-code">405</div>', $content);
        $this->assertStringContainsString('Tests\\Kernel\\Exception8<div class="file">#FILE', $content);
        $this->assertStringContainsString('<p id="sub-title">0 hello world</p>', $content);
        $this->assertSame(405, $resultResponse->getStatusCode());
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

        $this->assertInstanceof(Response::class, $resultResponse = $this->invokeTestMethod($runtime, 'rendorWithHttpExceptionView', [$e]));

        $content = $resultResponse->getContent();

        $this->assertStringContainsString('Tests\\Kernel\\Exception8: hello world', $content);
        $this->assertSame(405, $resultResponse->getStatusCode());
    }

    /**
     * @api(
     *     title="render 调试关闭异常渲染",
     *     description="",
     *     note="",
     * )
     */
    public function testRenderWithDebugIsOff(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(Request::class);

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

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        $content = $resultResponse->getContent();

        $this->assertStringContainsString('<div id="status-code">500</div>', $content);
        $this->assertStringContainsString('<p id="title">服务器内部错误</p>', $content);
        $this->assertStringContainsString('<p id="sub-title">0 服务器遇到错误，无法完成请求</p>', $content);
        $this->assertSame(500, $resultResponse->getStatusCode());
    }

    /**
     * @api(
     *     title="render 调试开启异常渲染",
     *     description="",
     *     note="",
     * )
     */
    public function testRenderWithDebugIsOn(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(Request::class);

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

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

        $content = $resultResponse->getContent();

        $this->assertStringContainsString('Tests\\Kernel\\Exception1: hello world', $content);
        $this->assertSame(500, $resultResponse->getStatusCode());
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
        $this->invokeTestMethod($runtime, 'rendorWithHttpExceptionView', [$e]);
    }

    public function testRenderForEntityNotFoundException(): void
    {
        $app = new AppRuntime($container = new Container(), $appPath = __DIR__.'/app');

        $request = $this->createMock(Request::class);

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

        $this->assertInstanceof(Response::class, $resultResponse = $runtime->render($request, $e));

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

class Runtime11 extends ExceptionRuntime
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

class Runtime22 extends ExceptionRuntime
{
    public function getHttpExceptionView(HttpException $e): string
    {
        return __DIR__.'/assert/'.$e->getStatusCode().'.php';
    }

    public function getDefaultHttpExceptionView(): string
    {
        return '';
    }
}

class Runtime3 extends ExceptionRuntime
{
    public function getHttpExceptionView(HttpException $e): string
    {
        return __DIR__.'/assert/notFound.php';
    }

    public function getDefaultHttpExceptionView(): string
    {
        return __DIR__.'/assert/default.php';
    }
}

class Runtime4 extends ExceptionRuntime
{
    public function getHttpExceptionView(HttpException $e): string
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
    public function report(): void
    {
        $_SERVER['testExceptionItSelfWithReport'] = 1;
    }
}

class Exception3 extends Exception
{
    public function render(Request $request, Exception $e): string
    {
        return 'hello world';
    }
}

class Exception4 extends Exception
{
    public function render(Request $request, Exception $e): Response
    {
        return new Response('foo bar', 500);
    }
}

class Exception5 extends Exception
{
    public function render(Request $request, Exception $e): array
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
