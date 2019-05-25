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

namespace Tests\Router;

use Leevel\Di\Container;
use Leevel\Kernel\App;
use Leevel\Router\MiddlewareParser;
use Leevel\Router\Router;
use Leevel\Router\ScanRouter;
use Leevel\Router\Url;
use Tests\TestCase;

/**
 * scanRouter test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.13
 *
 * @version 1.0
 */
class ScanRouterTest extends TestCase
{
    public function testBaseUse()
    {
        $middlewareParser = $this->createMiddlewareParser();

        $scanRouter = new ScanRouter($middlewareParser);

        $data = file_get_contents(__DIR__.'/Apps/AppScanRouter/data.json');

        $this->assertSame(
            $data,
            $this->varJson(
                $scanRouter->handle()
            )
        );

        Container::singletons()->clear();
    }

    protected function createMiddlewareParser(): MiddlewareParser
    {
        return new MiddlewareParser($this->createRouter());
    }

    protected function createRouter(): Router
    {
        $container = Container::singletons();
        $app = new App($container, '');
        $app->setAppPath(__DIR__.'/Apps/AppScanRouter');
        $app->setPath(__DIR__.'/Apps/AppScanRouter');
        $router = new Router($container);

        $router->setMiddlewareGroups([
            'group1' => [
                'demo1',
                'demo2',
            ],

            'group2' => [
                'demo1',
                'demo3:10,world',
            ],

            'group3' => [
                'demo1',
                'demo2',
                'demo3:10,world',
            ],
        ]);

        $router->setMiddlewareAlias([
            'demo1' => 'Tests\\Router\\Middlewares\\Demo1',
            'demo2' => 'Tests\\Router\\Middlewares\\Demo2',
            'demo3' => 'Tests\\Router\\Middlewares\\Demo3',
        ]);

        $container->singleton('app', $app);
        $container->singleton('url', new Url1());
        $container->singleton('router', $router);

        return $router;
    }
}

class Url1 extends Url
{
    public function __construct()
    {
    }

    public function getDomain(): string
    {
        return 'queryphp.com';
    }
}
