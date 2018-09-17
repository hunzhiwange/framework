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

namespace Tests\Router;

use Leevel\Di\Container;
use Leevel\Router\MiddlewareParser;
use Leevel\Router\Router;
use Leevel\Router\ScanRouter;
use Leevel\Support\Facade;
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
            $this->varJsonEncode(
                $scanRouter->handle(),
                __FUNCTION__
            )
        );

        // 静态属性会保持住，可能受到其它单元测试的影响
        Facade::remove('project');
        Facade::remove('url');
        Facade::remove('router');
        Facade::setContainer(null);
    }

    protected function createMiddlewareParser(): MiddlewareParser
    {
        return new MiddlewareParser($this->createRouter());
    }

    protected function createRouter(): Router
    {
        $router = new Router($container = new Leevel1());

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

        $router->setControllerDir('Tests\\Router\\Apps');

        $container->singleton('project', $container);
        $container->singleton('url', new Url1());
        $container->singleton('router', $router);

        // 静态属性会保持住，可能受到其它单元测试的影响
        Facade::remove('project');
        Facade::remove('url');
        Facade::remove('router');

        Facade::setContainer($container);

        return $router;
    }
}

class Leevel1 extends Container
{
    public function appPath()
    {
        return __DIR__.'/Apps/AppScanRouter';
    }
}

class Url1
{
    public function getDomain()
    {
        return 'queryphp.com';
    }
}
