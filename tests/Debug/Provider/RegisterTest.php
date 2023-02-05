<?php

declare(strict_types=1);

namespace Tests\Debug\Provider;

use Leevel\Cache\File as CacheFile;
use Leevel\Debug\Debug;
use Leevel\Debug\Provider\Register;
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

/**
 * @internal
 *
 * @coversNothing
 */
final class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createApp()->container());

        $test->register();

        $container->alias($test->providers());

        $debug = $container->make('debug');

        $this->assertInstanceof(Debug::class, $debug);

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response);

        $content = $response->getContent();

        static::assertStringContainsString('{"foo":"bar",":trace":', $content);

        static::assertStringContainsString('"php":{"version":', $content);

        static::assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    protected function createApp(): App
    {
        $app = new App($container = new Container(), '');

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

    protected function createLog(): ILog
    {
        $log = new LogFile([
            'path' => __DIR__.'/cacheLog',
        ]);

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
}

class App extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}
