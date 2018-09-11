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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Log\Middleware;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Fso;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Log\Manager;
use Leevel\Log\Middleware\Log as MiddlewareLog;
use Leevel\Option\Option;
use Tests\TestCase;

/**
 * log test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.24
 *
 * @version 1.0
 */
class LogTest extends TestCase
{
    public function testBaseUse()
    {
        $log = $this->createLog();

        $middleware = new MiddlewareLog($log);

        $request = $this->createRequest();
        $response = $this->createResponse();

        $log->info('foo', ['bar']);
        $filePath = __DIR__.'/cache/development.info/'.date('Y-m-d').'.log';
        $this->assertFileNotExists($filePath);

        $this->assertNull($middleware->terminate(function ($request, $response) {
            $this->assertInstanceof(IRequest::class, $request);
            $this->assertInstanceof(IResponse::class, $response);
            $this->assertSame('content', $response->getContent());
        }, $request, $response));

        $this->assertFileExists($filePath);

        Fso::deleteDirectory(__DIR__.'/cache', true);
    }

    protected function createRequest(): IRequest
    {
        return $this->createMock(IRequest::class);
    }

    protected function createResponse(): IResponse
    {
        $response = $this->createMock(IResponse::class);

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
                'levels'   => [
                    'debug',
                    'info',
                    'notice',
                    'warning',
                    'error',
                    'critical',
                    'alert',
                    'emergency',
                ],
                'channel'     => 'development',
                'buffer'      => true,
                'buffer_size' => 100,
                'connect'     => [
                    'file' => [
                        'driver'  => 'file',
                        'channel' => null,
                        'name'    => 'Y-m-d',
                        'size'    => 2097152,
                        'path'    => __DIR__.'/cache',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        return $manager;
    }
}
