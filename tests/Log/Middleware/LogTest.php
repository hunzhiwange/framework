<?php

declare(strict_types=1);

namespace Tests\Log\Middleware;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Filesystem\Helper;
use Leevel\Http\Request;
use Leevel\Http\Response;
use Leevel\Log\ILog;
use Leevel\Log\Manager;
use Leevel\Log\Middleware\Log as MiddlewareLog;
use Leevel\Option\Option;
use Tests\TestCase;

class LogTest extends TestCase
{
    public function testBaseUse(): void
    {
        $log = $this->createLog();

        $middleware = new MiddlewareLog($log);

        $request = $this->createRequest();
        $response = $this->createResponse();

        $log->info('foo', ['bar']);
        $filePath = __DIR__.'/cache/development.info/'.ILog::DEFAULT_MESSAGE_CATEGORY.'-'.date('Y-m-d').'.log';
        $this->assertFileDoesNotExist($filePath);

        $this->assertNull($middleware->terminate(function ($request, $response) {
            $this->assertInstanceof(Request::class, $request);
            $this->assertInstanceof(Response::class, $response);
            $this->assertSame('content', $response->getContent());
        }, $request, $response));

        $this->assertFileExists($filePath);

        Helper::deleteDirectory(__DIR__.'/cache');
    }

    protected function createRequest(): Request
    {
        return $this->createMock(Request::class);
    }

    protected function createResponse(): Response
    {
        $response = $this->createMock(Response::class);

        $response->method('getContent')->willReturn('content');
        $this->assertEquals('content', $response->getContent());

        return $response;
    }

    protected function createLog(): Manager
    {
        $container = new Container();

        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'log' => [
                'default'  => 'file',
                'level'    => [
                    ILog::DEFAULT_MESSAGE_CATEGORY => 'debug',
                ],
                'channel'     => 'development',
                'buffer'      => true,
                'buffer_size' => 100,
                'connect'     => [
                    'file' => [
                        'driver'          => 'file',
                        'channel'         => null,
                        'name'            => 'Y-m-d',
                        'path'            => __DIR__.'/cache',
                        'format'          => 'Y-m-d H:i:s u',
                        'file_permission' => null,
                        'use_locking'     => false,
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        $eventDispatch = $this->createMock(IDispatch::class);

        $this->assertNull($eventDispatch->handle('event'));

        $container->singleton('event', $eventDispatch);

        return $manager;
    }
}
