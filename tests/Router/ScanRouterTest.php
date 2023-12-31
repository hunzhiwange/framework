<?php

declare(strict_types=1);

namespace Tests\Router;

use Leevel\Di\Container;
use Leevel\Kernel\App;
use Leevel\Router\MiddlewareParser;
use Leevel\Router\Router;
use Leevel\Router\ScanRouter;
use Tests\TestCase;

final class ScanRouterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $middlewareParser = $this->createMiddlewareParser();

        $scanRouter = new ScanRouter(
            $middlewareParser,
            [__DIR__.'/Apps/AppScanRouter'],
            [],
            [
                'pet' => [],
                'store' => [],
                'user' => [],
                '/api/v1' => [
                    'middlewares' => 'group1',
                ],
                '/api/v2' => [
                    'middlewares' => 'group2',
                ],
                '/api/v3' => [
                    'middlewares' => 'demo1,demo3:30,world',
                ],
                '/api/v3' => [
                    'middlewares' => ['demo1', 'group3'],
                ],
                '/api/v4' => [
                    'middlewares' => 'notFound',
                ],
            ],
        );
        $scanRouter->setControllerDir('');

        $data = file_get_contents(__DIR__.'/Apps/AppScanRouter/data.json');

        static::assertSame(
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
        $container->singleton('router', $router);

        return $router;
    }
}
