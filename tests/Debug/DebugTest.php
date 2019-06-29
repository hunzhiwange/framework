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

namespace Tests\Debug;

use Error;
use Exception;
use Leevel\Database\IDatabase;
use Leevel\Debug\Debug;
use Leevel\Di\Container;
use Leevel\Event\Dispatch;
use Leevel\Event\IDispatch;
use Leevel\Http\JsonResponse;
use Leevel\Http\Request;
use Leevel\Http\Response;
use Leevel\Kernel\App as Apps;
use Leevel\Log\File as LogFile;
use Leevel\Log\ILog;
use Leevel\Option\IOption;
use Leevel\Option\Option;
use Leevel\Session\File as SessionFile;
use Leevel\Session\ISession;
use Tests\Database;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * debug test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.24
 *
 * @version 1.0
 *
 * @api(
 *     title="Debug",
 *     path="component/debug",
 *     description="添加一个组件调试。",
 * )
 */
class DebugTest extends TestCase
{
    use Database;

    public function testBaseUse(): void
    {
        $debug = $this->createDebug();

        $this->createApp();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        // twice same with once
        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new Response();

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('<link rel="stylesheet" type="text/css" href="/debugbar/vendor/font-awesome/css/font-awesome.min.css">', $content);

        $this->assertStringContainsString('<link rel="stylesheet" type="text/css" href="/debugbar/debugbar.css">', $content);

        $this->assertStringContainsString('var phpdebugbar = new PhpDebugBar.DebugBar()', $content);

        $this->assertStringContainsString("console.log( '%cThe PHP Framework For Code Poem As Free As Wind %c(http://www.queryphp.com)', 'font-weight: bold;color: #06359a;', 'color: #02d629;' );", $content);

        $this->assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    /**
     * @api(
     *     title="JSON 关联数组调试",
     *     description="关联数组结构会在尾部追加一个选项 `:trace` 用于调试。
     *
     * **返回结构**
     *
     * ``` php
     * $response = [\"foo\" => \"bar\", \":trace\" => []];
     * ```
     *
     * 关联数组在尾部追加一个选项作为调试信息，这与非关联数组有所不同。
     * ",
     *     note="",
     * )
     */
    public function testJson(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('{"foo":"bar",":trace":', $content);

        $this->assertStringContainsString('"php":{"version":', $content);

        $this->assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    /**
     * @api(
     *     title="JSON 非关联数组调试",
     *     description="非关联数组结构会在尾部追加一个 `:trace` 用于调试。
     *
     * **返回结构**
     *
     * ``` php
     * $response = [\"foo\", \"bar\", [\":trace\" => []]];
     * ```
     *
     * 非关联数组在尾部追加一个调试信息，将不会破坏返回接口的 JSON 结构。
     * ",
     *     note="",
     * )
     */
    public function testJsonForNotAssociativeArray(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo', 'bar']);

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('"foo","bar",{":trace":{', $content);

        $this->assertStringContainsString('"php":{"version":', $content);

        $this->assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    /**
     * @api(
     *     title="关闭调试",
     *     description="",
     *     note="",
     * )
     */
    public function testDisable(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('{"foo":"bar",":trace":', $content);

        $this->assertStringContainsString('"php":{"version":', $content);

        $this->assertStringContainsString('Starts from this moment with QueryPHP.', $content);

        $debug->disable();

        $response2 = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response2);

        $content = $response2->getContent();

        $this->assertStringNotContainsString('{"foo":"bar",":trace":', $content);

        $this->assertStringNotContainsString('"php":{"version":', $content);

        $this->assertStringNotContainsString('Starts from this moment with QueryPHP.', $content);
    }

    /**
     * @api(
     *     title="启用调试",
     *     description="",
     *     note="",
     * )
     */
    public function testEnable(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->disable();

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringNotContainsString('{"foo":"bar",":trace":', $content);

        $this->assertStringNotContainsString('"php":{"version":', $content);

        $this->assertStringNotContainsString('Starts from this moment with QueryPHP.', $content);

        $this->assertTrue($debug->isBootstrap());

        $debug->enable();

        $this->assertTrue($debug->isBootstrap());

        $response2 = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response2);

        $content = $response2->getContent();

        $this->assertStringContainsString('{"foo":"bar",":trace":', $content);

        $this->assertStringContainsString('"php":{"version":', $content);

        $this->assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    /**
     * @api(
     *     title="启用调试但是未初始化",
     *     description="",
     *     note="",
     * )
     */
    public function testEnableWithoutBootstrap(): void
    {
        $debug = $this->createDebug();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->disable();

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringNotContainsString('{"foo":"bar",":trace":', $content);

        $this->assertStringNotContainsString('"php":{"version":', $content);

        $this->assertStringNotContainsString('Starts from this moment with QueryPHP.', $content);

        $this->assertFalse($debug->isBootstrap());

        $debug->enable();

        $this->assertTrue($debug->isBootstrap());

        $response2 = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response2);

        $content = $response2->getContent();

        $this->assertStringContainsString('{"foo":"bar",":trace":', $content);

        $this->assertStringContainsString('"php":{"version":', $content);

        $this->assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    public function testEnableTwiceSameWithOne(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->disable();

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringNotContainsString('{"foo":"bar",":trace":', $content);

        $this->assertStringNotContainsString('"php":{"version":', $content);

        $this->assertStringNotContainsString('Starts from this moment with QueryPHP.', $content);

        $this->assertTrue($debug->isBootstrap());

        $debug->enable();

        $this->assertTrue($debug->isBootstrap());

        $response2 = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response2);

        $content = $response2->getContent();

        $this->assertStringContainsString('{"foo":"bar",":trace":', $content);

        $this->assertStringContainsString('"php":{"version":', $content);

        $this->assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    /**
     * @dataProvider getMessageLevelsData
     *
     * @param string $level
     *
     * @api(
     *     title="调试消息等级",
     *     description="
     * **支持的消息类型**
     *
     * ``` php
     * ".\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Debug\DebugTest::class, 'getMessageLevelsData')."
     * ```
     *
     * 系统支持多种消息类型，可以参考这个进行调试。
     * ",
     *     note="",
     * )
     */
    public function testMessageLevelsData(string $level): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        foreach (['hello', 'world'] as $v) {
            $debug->{$level}($v);
        }

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('{"foo":"bar",":trace":', $content);
        $this->assertStringContainsString('"php":{"version":', $content);
        $this->assertStringContainsString('Starts from this moment with QueryPHP.', $content);
        $this->assertStringContainsString('{"message":"hello","message_html":null,"is_string":true,"label":"'.$level.'",', $content);
        $this->assertStringContainsString('{"message":"world","message_html":null,"is_string":true,"label":"'.$level.'",', $content);
    }

    public function getMessageLevelsData()
    {
        return [
            ['emergency'], ['alert'], ['critical'],
            ['error'], ['warning'], ['notice'],
            ['info'], ['debug'], ['log'],
        ];
    }

    /**
     * @api(
     *     title="调试 Session",
     *     description="",
     *     note="",
     * )
     */
    public function testWithSession(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $session = $debug->getContainer()->make('session');

        $session->set('test_session', 'test_value');

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('"session":{"test_session":"test_value"},', $content);
    }

    /**
     * @api(
     *     title="调试 Log",
     *     description="",
     *     note="",
     * )
     */
    public function testWithLog(): void
    {
        $debug = $this->createDebugWithLog();

        $container = $debug->getContainer();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $log = $container->make('log');

        $log->info('test_log', ['exends' => 'bar']);
        $log->debug('test_log_debug');

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('"logs":{"count":2,', $content);

        $this->assertStringContainsString('test_log info: {\"exends\":\"bar\"}', $content);

        $this->assertStringContainsString('test_log_debug debug: []', $content);
    }

    /**
     * @api(
     *     title="调试数据库",
     *     description="",
     *     note="",
     * )
     */
    public function testWithDatabase(): void
    {
        $debug = $this->createDebugWithDatabase();
        $container = $debug->getContainer();
        $this->assertFalse($debug->isBootstrap());
        $debug->bootstrap();
        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $database = $container->make('database');

        $database
            ->table('guest_book')
            ->findAll();

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('"logs":{"count":1,', $content);
        $this->assertStringContainsString('SELECT `guest_book`.* FROM `guest_book`: []', $content);
    }

    /**
     * @api(
     *     title="调试时间",
     *     description="",
     *     note="",
     * )
     */
    public function testTime(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->time('time_test');

        $debug->end('time_test');

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('"time":{"start"', $content);

        $this->assertStringContainsString('"measures":[{"label":"time_test","start":', $content);
    }

    /**
     * @api(
     *     title="调试带有标签的时间",
     *     description="",
     *     note="",
     * )
     */
    public function testTimeWithLabel(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->time('time_test', 'time_label');

        $debug->end('time_test');

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('"time":{"start"', $content);

        $this->assertStringContainsString('"measures":[{"label":"time_label","start":', $content);
    }

    public function testEndWithNoStartDoNothing(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->end('time_without_start');

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('"time":{"start"', $content);

        $this->assertStringContainsString('"measures":[]', $content);
    }

    public function testAddTime(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->addTime('time_test', 1, 5);

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('"time":{"start"', $content);

        $this->assertStringContainsString('"measures":[{"label":"time_test","start":1', $content);

        $this->assertStringContainsString('"end":5,', $content);

        $this->assertStringContainsString('"relative_end":5,', $content);

        $this->assertStringContainsString('"duration":4,', $content);

        $this->assertStringContainsString('"duration_str":"4s",', $content);
    }

    public function testClosureTime(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->closureTime('time_test', function () {
        });

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('"time":{"start"', $content);

        $this->assertStringContainsString('"measures":[{"label":"time_test","start":', $content);
    }

    public function testException(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->exception(new Exception('test_exception'));

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('"exceptions":{"count":1,"exceptions":[', $content);

        $this->assertStringContainsString('"type":"Exception",', $content);

        $this->assertStringContainsString('"message":"test_exception",', $content);

        $this->assertStringContainsString('"code":0,', $content);

        $this->assertStringContainsString('$response = new JsonResponse([\'foo\' => \'bar\']);', $content);

        $this->assertStringContainsString('$debug->exception(new Exception(\'test_exception\'));', $content);
    }

    public function testExceptionWithError(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->exception(new Error('test_error'));

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('"exceptions":{"count":1,"exceptions":[', $content);

        $this->assertStringContainsString('"type":"Error",', $content);

        $this->assertStringContainsString('"message":"test_error",', $content);

        $this->assertStringContainsString('"code":0,', $content);

        $this->assertStringContainsString('$response = new JsonResponse([\'foo\' => \'bar\']);', $content);

        $this->assertStringContainsString('$debug->exception(new Error(\'test_error\'));', $content);
    }

    public function testSetOptionWithoutJson(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('{"foo":"bar",":trace":', $content);

        $this->assertStringContainsString('"php":{"version":', $content);

        $this->assertStringContainsString('Starts from this moment with QueryPHP.', $content);

        $debug->setOption('json', false);

        $response2 = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response2);

        $content = $response2->getContent();

        $this->assertStringNotContainsString('{"foo":"bar",":trace":', $content);

        $this->assertStringNotContainsString('"php":{"version":', $content);

        $this->assertStringNotContainsString('Starts from this moment with QueryPHP.', $content);
    }

    public function testSetOptionWithoutJavascriptAndConsole(): void
    {
        $debug = $this->createDebug();

        $this->assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        // twice same with once
        $debug->bootstrap();

        $this->assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new Response();

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertStringContainsString('<link rel="stylesheet" type="text/css" href="/debugbar/vendor/font-awesome/css/font-awesome.min.css">', $content);

        $this->assertStringContainsString('<link rel="stylesheet" type="text/css" href="/debugbar/debugbar.css">', $content);

        $this->assertStringContainsString('var phpdebugbar = new PhpDebugBar.DebugBar()', $content);

        $this->assertStringContainsString("console.log( '%cThe PHP Framework For Code Poem As Free As Wind %c(http://www.queryphp.com)', 'font-weight: bold;color: #06359a;', 'color: #02d629;' );", $content);

        $this->assertStringContainsString('Starts from this moment with QueryPHP.', $content);

        $debug->setOption('javascript', false);
        $debug->setOption('console', false);

        $response2 = new Response();

        $debug->handle($request, $response2);

        $content = $response2->getContent();

        $this->assertStringNotContainsString('<link rel="stylesheet" type="text/css" href="/debugbar/vendor/font-awesome/css/font-awesome.min.css">', $content);

        $this->assertStringNotContainsString('<link rel="stylesheet" type="text/css" href="/debugbar/debugbar.css">', $content);

        $this->assertStringNotContainsString('var phpdebugbar = new PhpDebugBar.DebugBar()', $content);

        $this->assertStringNotContainsString("console.log( '%cThe PHP Framework For Code Poem As Free As Wind %c(http://www.queryphp.com)', 'font-weight: bold;color: #06359a;', 'color: #02d629;' );", $content);

        $this->assertStringNotContainsString('Starts from this moment with QueryPHP.', $content);

        $this->assertSame('', $content);
    }

    protected function createDebugWithLog(): Debug
    {
        return new Debug($this->createAppWithLog()->container());
    }

    protected function createAppWithLog(): App
    {
        $app = new App($container = new Container(), '');

        $container->instance('app', $app);

        $container->instance('session', $this->createSession());

        $container->instance('option', $this->createOption());

        $eventDispatch = new Dispatch($container);
        $container->singleton(IDispatch::class, $eventDispatch);

        $container->instance('log', $this->createLog($eventDispatch));

        return $app;
    }

    protected function createDebugWithDatabase(): Debug
    {
        return new Debug($this->createAppWithDatabase()->container());
    }

    protected function createAppWithDatabase(): App
    {
        $app = new App($container = new Container(), '');

        $container->instance('app', $app);

        $container->instance('session', $this->createSession());

        $container->instance('option', $this->createOption());

        $eventDispatch = new Dispatch($container);
        $container->singleton(IDispatch::class, $eventDispatch);

        $container->instance('database', $this->createDatabase($eventDispatch));

        return $app;
    }

    protected function createDebug(): Debug
    {
        return new Debug($this->createApp()->container());
    }

    protected function createApp(): App
    {
        $app = new App($container = new Container(), '');

        $container->instance('app', $app);

        $container->instance('session', $this->createSession());

        $container->instance('log', $this->createLog());

        $container->instance('option', $this->createOption());

        $eventDispatch = $this->createMock(IDispatch::class);

        $this->assertNull($eventDispatch->handle('event'));

        $container->singleton(IDispatch::class, $eventDispatch);

        return $app;
    }

    protected function createSession(): ISession
    {
        $session = new SessionFile([
            'path' => __DIR__.'/cacheFile',
        ]);

        $this->assertInstanceof(ISession::class, $session);

        return $session;
    }

    protected function createLog(?IDispatch $dispatch = null): ILog
    {
        $log = new LogFile([
            'path' => __DIR__.'/cacheLog',
        ], $dispatch);

        $this->assertInstanceof(ILog::class, $log);

        return $log;
    }

    protected function createDatabase(?IDispatch $dispatch = null): IDatabase
    {
        $database = $this->createDatabaseConnect($dispatch);
        $this->assertInstanceof(IDatabase::class, $database);

        return $database;
    }

    protected function createOption(): IOption
    {
        $data = [
            'app' => [
                'environment'       => 'environment',
            ],
            'debug' => [
                'json'       => true,
                'console'    => true,
                'javascript' => true,
            ],
        ];

        $option = new Option($data);

        $this->assertInstanceof(IOption::class, $option);

        return $option;
    }

    protected function getDatabaseTable(): array
    {
        return ['guest_book'];
    }
}

class App extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}
