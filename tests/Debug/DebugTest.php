<?php

declare(strict_types=1);

namespace Tests\Debug;

use Leevel\Cache\File as CacheFile;
use Leevel\Config\Config;
use Leevel\Config\IConfig;
use Leevel\Database\IDatabase;
use Leevel\Debug\Debug;
use Leevel\Di\Container;
use Leevel\Event\Dispatch;
use Leevel\Event\IDispatch;
use Leevel\Http\JsonResponse;
use Leevel\Http\Request;
use Leevel\Http\Response;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Utils\Api;
use Leevel\Log\File as LogFile;
use Leevel\Log\ILog;
use Leevel\Session\File as SessionFile;
use Leevel\Session\ISession;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Database;
use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'zh-CN:title' => 'Debug',
    'path' => 'component/debug',
    'zh-CN:description' => <<<'EOT'
添加一个组件调试。
EOT,
])]
final class DebugTest extends TestCase
{
    use Database;

    public function testBaseUse(): void
    {
        $debug = $this->createDebug();

        $this->createApp();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        // twice same with once
        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new Response();

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringContainsString("console.log( '%cThe PHP Framework For Code Poem As Free As Wind %c(http://www.queryphp.com)', 'font-weight: bold;color: #06359a;', 'color: #02d629;' );", $content);
        self::assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    #[Api([
        'zh-CN:title' => 'JSON 关联数组调试',
        'zh-CN:description' => <<<'EOT'
关联数组结构会在尾部追加一个选项 `:trace` 用于调试。

**返回结构**

``` php
$response = ["foo" => "bar", ":trace" => []];
```

关联数组在尾部追加一个选项作为调试信息，这与非关联数组有所不同。
EOT,
    ])]
    public function testJson(): void
    {
        $debug = $this->createDebug();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringContainsString('{"foo":"bar",":trace":', $content);

        self::assertStringContainsString('"php":{"version":', $content);

        self::assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    #[Api([
        'zh-CN:title' => 'JSON 非关联数组不支持调试',
        'zh-CN:description' => <<<'EOT'
非关联数组结构会在不支持调试。

**返回结构**

``` php
$response = ["foo", "bar"];
```
EOT,
    ])]
    public function testJsonForNotAssociativeArray(): void
    {
        $debug = $this->createDebug();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo', 'bar']);

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringContainsString('"foo","bar"', $content);
    }

    #[Api([
        'zh-CN:title' => '关闭调试',
    ])]
    public function testDisable(): void
    {
        $debug = $this->createDebug();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringContainsString('{"foo":"bar",":trace":', $content);

        self::assertStringContainsString('"php":{"version":', $content);

        self::assertStringContainsString('Starts from this moment with QueryPHP.', $content);

        $debug->disable();

        $response2 = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response2);

        $content = $response2->getContent();

        self::assertStringNotContainsString('{"foo":"bar",":trace":', $content);

        self::assertStringNotContainsString('"php":{"version":', $content);

        self::assertStringNotContainsString('Starts from this moment with QueryPHP.', $content);
    }

    #[Api([
        'zh-CN:title' => '启用调试',
    ])]
    public function testEnable(): void
    {
        $debug = $this->createDebug();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->disable();

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringNotContainsString('{"foo":"bar",":trace":', $content);

        self::assertStringNotContainsString('"php":{"version":', $content);

        self::assertStringNotContainsString('Starts from this moment with QueryPHP.', $content);

        self::assertTrue($debug->isBootstrap());

        $debug->enable();

        self::assertTrue($debug->isBootstrap());

        $response2 = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response2);

        $content = $response2->getContent();

        self::assertStringContainsString('{"foo":"bar",":trace":', $content);

        self::assertStringContainsString('"php":{"version":', $content);

        self::assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    #[Api([
        'zh-CN:title' => '启用调试但是未初始化',
    ])]
    public function testEnableWithoutBootstrap(): void
    {
        $debug = $this->createDebug();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->disable();

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringNotContainsString('{"foo":"bar",":trace":', $content);

        self::assertStringNotContainsString('"php":{"version":', $content);

        self::assertStringNotContainsString('Starts from this moment with QueryPHP.', $content);

        self::assertFalse($debug->isBootstrap());

        $debug->enable();

        self::assertTrue($debug->isBootstrap());

        $response2 = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response2);

        $content = $response2->getContent();

        self::assertStringContainsString('{"foo":"bar",":trace":', $content);

        self::assertStringContainsString('"php":{"version":', $content);

        self::assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    public function testEnableTwiceSameWithOne(): void
    {
        $debug = $this->createDebug();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->disable();

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringNotContainsString('{"foo":"bar",":trace":', $content);

        self::assertStringNotContainsString('"php":{"version":', $content);

        self::assertStringNotContainsString('Starts from this moment with QueryPHP.', $content);

        self::assertTrue($debug->isBootstrap());

        $debug->enable();

        self::assertTrue($debug->isBootstrap());

        $response2 = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response2);

        $content = $response2->getContent();

        self::assertStringContainsString('{"foo":"bar",":trace":', $content);

        self::assertStringContainsString('"php":{"version":', $content);

        self::assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    #[DataProvider('provideMessageLevelsDataCases')]
    #[Api([
        'zh-CN:title' => '调试消息等级',
        'zh-CN:description' => <<<'EOT'
**支持的消息类型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Debug\DebugTest::class, 'getMessageLevelsData')]}
```

系统支持多种消息类型，可以参考这个进行调试。
EOT,
    ])]
    public function testMessageLevelsData(string $level): void
    {
        $debug = $this->createDebug();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        foreach (['hello', 'world'] as $v) {
            $debug->{$level}($v);
        }

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringContainsString('{"foo":"bar",":trace":', $content);
        self::assertStringContainsString('"php":{"version":', $content);
        self::assertStringContainsString('Starts from this moment with QueryPHP.', $content);
        self::assertStringContainsString('{"message":"hello","message_html":null,"message_json":null,"is_string":true,', $content);
        self::assertStringContainsString('{"message":"world","message_html":null,"message_json":null,"is_string":true,', $content);
        self::assertStringContainsString('"label":"'.$level.'",', $content);
    }

    public static function provideMessageLevelsDataCases(): iterable
    {
        return [
            ['emergency'], ['alert'], ['critical'],
            ['error'], ['warning'], ['notice'],
            ['info'], ['debug'], ['log'],
        ];
    }

    #[Api([
        'zh-CN:title' => '调试 Session',
    ])]
    public function testWithSession(): void
    {
        $debug = $this->createDebug();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $session = $debug->getContainer()->make('session');

        $session->set('test_session', 'test_value');

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringContainsString('"session":{"test_session":"test_value"},', $content);
    }

    #[Api([
        'zh-CN:title' => '调试 Log',
    ])]
    public function testWithLog(): void
    {
        $debug = $this->createDebugWithLog();

        $container = $debug->getContainer();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $log = $container->make('log');

        $log->info('test_log', ['exends' => 'bar']);
        $log->debug('test_log_debug');

        $debug->handle($request, $response);
        $content = $response->getContent();

        self::assertStringContainsString('"logs":{"count":2,', $content);
        self::assertStringContainsString('test_log info: {\"exends\":\"bar\"}', $content);
        self::assertStringContainsString('test_log_debug debug: []', $content);
    }

    #[Api([
        'zh-CN:title' => '调试数据库',
    ])]
    public function testWithDatabase(): void
    {
        $debug = $this->createDebugWithDatabase();
        $container = $debug->getContainer();
        self::assertFalse($debug->isBootstrap());
        $debug->bootstrap();
        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $database = $container->make('database');
        $database
            ->table('guest_book')
            ->findAll()
        ;

        $debug->handle($request, $response);
        $content = $response->getContent();

        self::assertStringContainsString('"logs":{"count":1,', $content);
        self::assertStringContainsString('SQL: [39] SELECT `guest_book`.* FROM `guest_book` | Params:  0', $content);
    }

    #[Api([
        'zh-CN:title' => '调试时间',
    ])]
    public function testTime(): void
    {
        $debug = $this->createDebug();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->time('time_test');

        $debug->end('time_test');

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringContainsString('"time":{"count":', $content);

        self::assertStringContainsString('"measures":[{"label":"time_test","start":', $content);
    }

    #[Api([
        'zh-CN:title' => '调试带有标签的时间',
    ])]
    public function testTimeWithLabel(): void
    {
        $debug = $this->createDebug();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->time('time_test', 'time_label');

        $debug->end('time_test');

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringContainsString('"time":{"count":', $content);

        self::assertStringContainsString('"measures":[{"label":"time_label","start":', $content);
    }

    public function testEndWithNoStartDoNothing(): void
    {
        $debug = $this->createDebug();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->end('time_without_start');

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringContainsString('"time":{"count":', $content);

        self::assertStringContainsString('"measures":[]', $content);
    }

    public function testAddTime(): void
    {
        $debug = $this->createDebug();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->addTime('time_test', 1, 5);

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringContainsString('"time":{"count":', $content);

        self::assertStringContainsString('"measures":[{"label":"time_test","start":1', $content);

        self::assertStringContainsString('"end":5,', $content);

        self::assertStringContainsString('"relative_end":5,', $content);

        self::assertStringContainsString('"duration":4,', $content);

        self::assertStringContainsString('"duration_str":"4s",', $content);
    }

    public function testClosureTime(): void
    {
        $debug = $this->createDebug();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->closureTime('time_test', static function (): void {});

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringContainsString('"time":{"count":', $content);

        self::assertStringContainsString('"measures":[{"label":"time_test","start":', $content);
    }

    public function testException(): void
    {
        $debug = $this->createDebug();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->exception(new \Exception('test_exception'));

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringContainsString('"exceptions":{"count":1,"exceptions":[', $content);

        self::assertStringContainsString('"type":"Exception",', $content);

        self::assertStringContainsString('"message":"test_exception",', $content);

        self::assertStringContainsString('"code":0,', $content);

        self::assertStringContainsString('$response = new JsonResponse([\'foo\' => \'bar\']);', $content);

        self::assertStringContainsString('$debug->exception(new \\\Exception(\'test_exception\'));', $content);
    }

    public function testExceptionWithError(): void
    {
        $debug = $this->createDebug();

        self::assertFalse($debug->isBootstrap());

        $debug->bootstrap();

        self::assertTrue($debug->isBootstrap());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->exception(new \Error('test_error'));

        $debug->handle($request, $response);

        $content = $response->getContent();

        self::assertStringContainsString('"exceptions":{"count":1,"exceptions":[', $content);

        self::assertStringContainsString('"type":"Error",', $content);

        self::assertStringContainsString('"message":"test_error",', $content);

        self::assertStringContainsString('"code":0,', $content);

        self::assertStringContainsString('$response = new JsonResponse([\'foo\' => \'bar\']);', $content);

        self::assertStringContainsString('$debug->exception(new \\\Error(\'test_error\'));', $content);
    }

    public function testWithoutJson(): void
    {
        $debug = $this->createDebug(['json' => false]);
        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);
        $debug->handle($request, $response);
        $content = $response->getContent();

        self::assertStringNotContainsString('{"foo":"bar",":trace":', $content);
        self::assertStringNotContainsString('"php":{"version":', $content);
        self::assertStringNotContainsString('Starts from this moment with QueryPHP.', $content);
    }

    public function testWithoutJavascriptAndConsole(): void
    {
        $debug = $this->createDebug([
            'javascript' => false,
            'console' => false,
        ]);
        $request = new Request();
        $response = new Response();
        $debug->handle($request, $response);
        $content = $response->getContent();

        self::assertStringNotContainsString('<link rel="stylesheet" type="text/css" href="/debugbar/vendor/font-awesome/css/font-awesome.min.css">', $content);
        self::assertStringNotContainsString('<link rel="stylesheet" type="text/css" href="/debugbar/debugbar.css">', $content);
        self::assertStringNotContainsString('var phpdebugbar = new PhpDebugBar.DebugBar()', $content);
        self::assertStringNotContainsString("console.log( '%cThe PHP Framework For Code Poem As Free As Wind %c(http://www.queryphp.com)', 'font-weight: bold;color: #06359a;', 'color: #02d629;' );", $content);
        self::assertStringNotContainsString('Starts from this moment with QueryPHP.', $content);
        self::assertSame('', $content);
    }

    public function testJsonStringToArray(): void
    {
        $debug = $this->createDebug();
        self::assertFalse($this->invokeTestMethod($debug, 'jsonStringToArray', [false]));
        self::assertFalse($this->invokeTestMethod($debug, 'jsonStringToArray', ["\xB1\x31"]));
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

        $container->instance('config', $this->createConfig());

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
        $container->instance('config', $this->createConfig());

        $eventDispatch = new Dispatch($container);
        $container->singleton(IDispatch::class, $eventDispatch);

        $container->instance('database', $this->createDatabase($eventDispatch));
        $container->instance('logs', $this->createLog($eventDispatch));

        return $app;
    }

    protected function createDebug(array $config = []): Debug
    {
        return new Debug($this->createApp()->container(), $config);
    }

    protected function createApp(): App
    {
        $app = new App($container = new Container(), '');

        $container->instance('app', $app);

        $container->instance('session', $this->createSession());

        $container->instance('log', $this->createLog());

        $container->instance('config', $this->createConfig());

        $eventDispatch = $this->createMock(IDispatch::class);

        self::assertNull($eventDispatch->handle('event'));

        $container->singleton(IDispatch::class, $eventDispatch);

        return $app;
    }

    protected function createSession(): ISession
    {
        $session = new SessionFile(new CacheFile([
            'path' => __DIR__.'/cacheFile',
        ]));

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

    protected function createConfig(): IConfig
    {
        $data = [
            'app' => [
                'environment' => 'environment',
            ],
            'debug' => [
                'json' => true,
                'console' => true,
                'javascript' => true,
            ],
        ];

        $config = new Config($data);

        $this->assertInstanceof(IConfig::class, $config);

        return $config;
    }

    protected function getDatabaseTable(): array
    {
        return ['guest_book'];
    }
}

class App extends Apps
{
    protected function registerBaseProvider(): void {}
}
