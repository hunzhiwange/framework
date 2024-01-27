<?php

declare(strict_types=1);

namespace Tests\Debug\Middleware;

use Leevel\Cache\File as CacheFile;
use Leevel\Config\Config;
use Leevel\Config\IConfig;
use Leevel\Debug\Debug;
use Leevel\Debug\Middleware\Debug as MiddlewareDebug;
use Leevel\Di\Container;
use Leevel\Event\IDispatch;
use Leevel\Http\Request;
use Leevel\Kernel\App as Apps;
use Leevel\Log\File as LogFile;
use Leevel\Log\ILog;
use Leevel\Session\File as SessionFile;
use Leevel\Session\ISession;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class DebugTest extends TestCase
{
    public function testBaseUse(): void
    {
        $debug = $this->createDebug();
        $app = $debug->getContainer()->make('app');

        $middleware = new MiddlewareDebug($app, $debug);

        $request = $this->createRequest('http://127.0.0.1');

        static::assertFalse($debug->isBootstrap());

        $middleware->handle(function (Request $request): Response {
            $this->assertSame('http://127.0.0.1', $request->getUri());

            return new Response();
        }, $request);

        static::assertTrue($debug->isBootstrap());
    }

    public function testHandleWithDebugIsFalse(): void
    {
        $debug = $this->createDebug(false);
        $app = $debug->getContainer()->make('app');

        $middleware = new MiddlewareDebug($app, $debug);

        $request = $this->createRequest('http://127.0.0.1');

        static::assertFalse($debug->isBootstrap());

        $middleware->handle(function (Request $request): Response {
            $this->assertSame('http://127.0.0.1', $request->getUri());

            return new Response();
        }, $request);

        static::assertFalse($debug->isBootstrap());
    }

    protected function createRequest(string $url): Request
    {
        $request = $this->createMock(Request::class);

        $request->method('getUri')->willReturn($url);
        static::assertSame($url, $request->getUri());

        return $request;
    }

    protected function createDebug(bool $debug = true): Debug
    {
        return new Debug($this->createApp($debug)->container());
    }

    protected function createApp(bool $debug = true): App
    {
        $app = new App($container = new Container(), '');
        $container->instance('app', $app);
        $container->instance('session', $this->createSession());
        $container->instance('log', $this->createLog());
        $container->instance('config', $this->createConfig($debug));

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

    protected function createLog(): ILog
    {
        $log = new LogFile([
            'path' => __DIR__.'/cacheLog',
        ]);

        $this->assertInstanceof(ILog::class, $log);

        return $log;
    }

    protected function createConfig(bool $debug = true): IConfig
    {
        $data = [
            'app' => [
                'environment' => 'environment',
                'debug' => $debug,
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
}

class App extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}
