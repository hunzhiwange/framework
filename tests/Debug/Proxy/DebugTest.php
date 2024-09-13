<?php

declare(strict_types=1);

namespace Tests\Debug\Proxy;

use Leevel\Cache\File as CacheFile;
use Leevel\Config\Config;
use Leevel\Config\IConfig;
use Leevel\Debug\Debug;
use Leevel\Debug\Proxy\Debug as ProxyDebug;
use Leevel\Di\Container;
use Leevel\Event\IDispatch;
use Leevel\Http\JsonResponse;
use Leevel\Http\Request;
use Leevel\Kernel\App as Apps;
use Leevel\Log\File as LogFile;
use Leevel\Log\ILog;
use Leevel\Session\File as SessionFile;
use Leevel\Session\ISession;
use Tests\TestCase;

final class DebugTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        $this->tearDown();
    }

    #[\Override]
    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $debug = $this->createDebug($container);
        $container->singleton('debug', static function () use ($debug): Debug {
            return $debug;
        });

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

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $debug = $this->createDebug($container);
        $container->singleton('debug', static function () use ($debug): Debug {
            return $debug;
        });

        self::assertFalse(ProxyDebug::proxy()->isBootstrap());
        ProxyDebug::proxy()->bootstrap();
        self::assertTrue(ProxyDebug::proxy()->isBootstrap());
        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);
        ProxyDebug::proxy()->handle($request, $response);
        $content = $response->getContent();
        self::assertStringContainsString('{"foo":"bar",":trace":', $content);
        self::assertStringContainsString('"php":{"version":', $content);
        self::assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    protected function createDebug(Container $container): Debug
    {
        return new Debug($this->createApplication($container)->container());
    }

    protected function createApplication(Container $container): App
    {
        $app = new App($container, '');
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

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}

class App extends Apps
{
    protected function registerBaseProvider(): void {}
}
