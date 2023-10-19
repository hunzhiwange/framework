<?php

declare(strict_types=1);

namespace Tests\Debug\Proxy;

use Leevel\Cache\File as CacheFile;
use Leevel\Debug\Debug;
use Leevel\Debug\Proxy\Debug as ProxyDebug;
use Leevel\Di\Container;
use Leevel\Event\IDispatch;
use Leevel\Http\JsonResponse;
use Leevel\Http\Request;
use Leevel\Kernel\App as Apps;
use Leevel\Log\File as LogFile;
use Leevel\Log\ILog;
use Leevel\Option\IOption;
use Leevel\Option\Option;
use Leevel\Session\File as SessionFile;
use Leevel\Session\ISession;
use Tests\TestCase;

final class DebugTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $debug = $this->createDebug($container);
        $container->singleton('debug', function () use ($debug): Debug {
            return $debug;
        });

        static::assertFalse($debug->isBootstrap());
        $debug->bootstrap();
        static::assertTrue($debug->isBootstrap());
        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);
        $debug->handle($request, $response);
        $content = $response->getContent();
        static::assertStringContainsString('{"foo":"bar",":trace":', $content);
        static::assertStringContainsString('"php":{"version":', $content);
        static::assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $debug = $this->createDebug($container);
        $container->singleton('debug', function () use ($debug): Debug {
            return $debug;
        });

        static::assertFalse(ProxyDebug::isBootstrap());
        ProxyDebug::bootstrap();
        static::assertTrue(ProxyDebug::isBootstrap());
        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);
        ProxyDebug::handle($request, $response);
        $content = $response->getContent();
        static::assertStringContainsString('{"foo":"bar",":trace":', $content);
        static::assertStringContainsString('"php":{"version":', $content);
        static::assertStringContainsString('Starts from this moment with QueryPHP.', $content);
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

        $container->instance('option', $this->createOption());

        $eventDispatch = $this->createMock(IDispatch::class);

        static::assertNull($eventDispatch->handle('event'));

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

    protected function createOption(): IOption
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

        $option = new Option($data);

        $this->assertInstanceof(IOption::class, $option);

        return $option;
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
    protected function registerBaseProvider(): void
    {
    }
}
