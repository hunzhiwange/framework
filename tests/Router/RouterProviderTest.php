<?php

declare(strict_types=1);

namespace Tests\Router;

use Leevel\Di\Container;
use Leevel\Kernel\App;
use Leevel\Kernel\Utils\Api;
use Leevel\Router\Router;
use Leevel\Router\RouterProvider;
use Leevel\Router\ScanRouter;
use Tests\Router\Middlewares\Demo1;
use Tests\Router\Middlewares\Demo2;
use Tests\Router\Middlewares\Demo3;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '路由服务提供者',
    'path' => 'router/provider',
    'zh-CN:description' => <<<'EOT'
路由主要由路由服务来接入框架，可以做一些设置。

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\App\Infra\Provider\Router::class)]}
```
EOT,
])]
final class RouterProviderTest extends TestCase
{
    protected function tearDown(): void
    {
        $file = __DIR__.'/router_cached.php';
        if (is_file($file)) {
            unlink($file);
        }
    }

    #[Api([
        'zh-CN:title' => '基本使用',
        'zh-CN:description' => <<<'EOT'
QueryPHP 路由最终结果主要由 `base_paths`、`groups` 和 `routers` 构成。

**fixture 定义**

**路由服务提供者 Tests\Router\RouterProvider1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\RouterProvider1::class)]}
```

**路由注解缓存结果 tests/Router/Apps/AppScanRouter/data.json**

``` json
{[file_get_contents('vendor/hunzhiwange/framework/tests/Router/Apps/AppScanRouter/data.json')]}
```
EOT,
    ])]
    public function testBaseUse(): void
    {
        $container = Container::singletons();
        $app = new App($container, '');
        $app->setAppPath(__DIR__.'/Apps/AppScanRouter');
        $app->setPath(__DIR__.'/Apps/AppScanRouter');
        $app->setRouterCachedPath(__DIR__.'/router_cached.php');

        $container->instance('app', $app);
        $container->instance('router', $router = $this->createRouter($container));

        $provider = new RouterProvider1($container);

        static::assertNull($provider->register());
        static::assertNull($provider->bootstrap());

        $data = file_get_contents(__DIR__.'/Apps/AppScanRouter/data.json');

        static::assertSame(
            $data,
            $this->varJson(
                [
                    'base_paths' => $router->getBasePaths(),
                    'groups' => $router->getGroups(),
                    'routers' => $router->getRouters(),
                ]
            )
        );

        Container::singletons()->clear();
    }

    public function testRouterIsCache(): void
    {
        $container = Container::singletons();
        $app = new App($container, '');
        $app->setAppPath(__DIR__.'/Apps/AppScanRouter');
        $app->setPath(__DIR__.'/Apps/AppScanRouter');
        $app->setRouterCachedPath(__DIR__.'/router_cached.php');

        $container->instance('app', $app);
        $container->singleton('router', $router = $this->createRouter($container));

        $routerData = file_get_contents(__DIR__.'/Apps/AppScanRouter/data.json');
        $routerData = '<?php return '.var_export(json_decode($routerData, true), true).';';
        file_put_contents(
            __DIR__.'/router_cached.php',
            $routerData,
        );

        $provider = new RouterProvider1($container);

        static::assertNull($provider->register());
        static::assertNull($provider->bootstrap());

        $data = file_get_contents(__DIR__.'/Apps/AppScanRouter/data.json');
        $this->varJson(
            [
                'base_paths' => $router->getBasePaths(),
                'groups' => $router->getGroups(),
                'routers' => $router->getRouters(),
            ]
        );
        static::assertSame(
            $data,
            $this->varJson(
                [
                    'base_paths' => $router->getBasePaths(),
                    'groups' => $router->getGroups(),
                    'routers' => $router->getRouters(),
                ]
            )
        );

        Container::singletons()->clear();
    }

    protected function createRouter(Container $container): Router
    {
        $router = new Router($container);

        $container->singleton('app', $container);
        $container->singleton('router', $router);

        return $router;
    }
}

class RouterProvider1 extends RouterProvider
{
    protected ?string $controllerDir = 'Tests\\Router\\Apps';

    protected array $middlewareGroups = [
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
    ];

    protected array $middlewareAlias = [
        'demo1' => Demo1::class,
        'demo2' => Demo2::class,
        'demo3' => Demo3::class,
    ];

    protected array $basePaths = [];

    protected array $groups = [
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
    ];

    public function bootstrap(): void
    {
        parent::bootstrap();
    }

    public function getRouters(): array
    {
        return parent::getRouters();
    }

    protected function makeScanRouter(): ScanRouter
    {
        $scanRouter = parent::makeScanRouter();
        $scanRouter->setControllerDir('');

        return $scanRouter;
    }
}
