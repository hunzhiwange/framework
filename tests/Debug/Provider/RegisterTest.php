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

namespace Tests\Debug\Provider;

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
 * register test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.25
 *
 * @version 1.0
 */
class RegisterTest extends TestCase
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

        $this->assertStringContainsString('{"foo":"bar",":trace":', $content);

        $this->assertStringContainsString('"php":{"version":', $content);

        $this->assertStringContainsString('Starts from this moment with QueryPHP.', $content);
    }

    protected function createApp(): App
    {
        $app = new App($container = new Container(), '');

        $container->instance('app', $app);

        $container->instance('session', $this->createSession());

        $container->instance('log', $this->createLog());

        $container->instance('option', $this->createOption());

        $eventDispatch = $this->createMock(IDispatch::class);

        $eventDispatch->method('handle')->willReturn(null);
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
}

class App extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}
