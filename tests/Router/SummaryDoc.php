<?php

declare(strict_types=1);

namespace Tests\Router;

use Leevel\Kernel\Utils\Api;

#[Api([
    'title' => 'Summary',
    'zh-CN:title' => '概述',
    'zh-TW:title' => '概述',
    'path' => 'router/index',
    'description' => <<<'EOT'
对于一个框架来说路由是一件非常重要的事情，可以说是框架的核心之一。路由的使用便捷和理解复杂度以及性能对整个框架来说至关重要。
EOT,
    'zh-CN:description' => <<<'EOT'
对于一个框架来说路由是一件非常重要的事情，可以说是框架的核心之一。路由的使用便捷和理解复杂度以及性能对整个框架来说至关重要。
EOT,
    'zh-TW:description' => <<<'EOT'
对于一个框架来说路由是一件非常重要的事情，可以说是框架的核心之一。路由的使用便捷和理解复杂度以及性能对整个框架来说至关重要。
EOT,
])]
class SummaryDoc
{
    #[Api([
        'zh-CN:title' => '路由解析',
        'zh-CN:description' => <<<'EOT'
QueryPHP 有一个非常独特的地方就是路由设计与其它框架有点出入，我们不需要像 Laravel 5、Thinkphp 5 等框架一样定义路由。

### Laravel 5

``` php
Route::middleware(['first', 'second'])->group(function () {
    Route::get('/', function () {
    });
    Route::get('user/profile', function () {
    });
});
```

### ThinkPHP 5

``` php
Route::group('blog', function () {
    Route::rule(':id', 'blog/read');
    Route::rule(':name', 'blog/read');
})->ext('html')->pattern(['id' => '\d+', 'name' => '\w+']);
```

### FastRoute

``` php
$r->addRoute('GET', '/user/{id:\d+}', 'handler');
$r->addRoute('GET', '/user/{name}', 'handler');
$r->addRoute('GET', '/user/{name:.+}', 'handler');
```

::: tip
其中 FastRoute 中提供一个路由 10 条路由合并匹配算法非常地高效，QueryPHP 已经吸收。 [合并路由匹配算法](http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html)
:::

### PHP 8 注解路由

QueryPHP 开始采用 PHP 8 属性来定义注解路由，可以很轻松地实现比较复杂的路由访问。

QueryPHP 框架提供 `MVC 自动路由` 并能够智能解析 Restful 请求和基于 PHP 8 属性的注解路由。
所以我们不需要定义一个 router.php 来注册我们的路由，并且能够优化并缓存起来加速解析。
EOT,
    ])]
    public function doc1(): void
    {
    }

    #[Api([
        'zh-CN:title' => '路由匹配过程',
        'zh-CN:description' => <<<'EOT'
QueryPHP 会优先进行`MVC 自动路由`匹配，也包含 `Restful` 路由匹配,如果匹配失败就会进行注解路由匹配阶段，如果还是
匹配失败则会抛出一个路由无法找到的异常。
EOT,
    ])]
    public function doc2(): void
    {
    }

    #[Api([
        'zh-CN:title' => '自动 MVC 路由',
        'zh-CN:description' => <<<'EOT'
很多时候我们不是特别关心它是 GET、POST 还是 PUT，我们就想简单输入一个地址就可以访问到我们的控制器。

路径  |  匹配控制器 |  备注
--|---|--
/  | App\Controller\Home::index()  |
/controller/action  | App\Controller\Controller::action()  |
/:blog/controller/action | Blog\Controller\Controller::action()  |  `:` 表示应用
/dir1/dir2/dir3/controller/action |  App\Controller\Dir1\Dir2\Dir3\Controller::action() |
/he_llo-wor/Bar/foo/xYY-ac/controller_xx-yy/action-xxx_Yzs  | AppController\HeLloWor\Bar\Foo\XYYAc\ControllerXxYy::actionXxxYzs()  |

::: warning
如果方法单独成为一个类，则对应的请求入口为 `handle`，我们推荐为每一个方法定义一个类，避免与其它方法冲突，而且路由匹配性能最佳。
框架底层会优先匹配方法单独成类，匹配失败则匹配控制器类对应方法操作，如果还是匹配失败进入注解路由匹配阶段。
:::

例如访问地址 `http://queryphp.cn/api/test`:
EOT,
        'note' => <<<'EOT'
上面这种就是一个简单粗暴的路由，简单有效，大多数时候可以满足我们系统开发的需求。
EOT,
        'lang' => 'php',
    ])]
    public function doc3(): void
    {
        <<<'EOT'
            <?php

            declare(strict_types=1);

            namespace App\Controller\Api;

            /**
            * api tests.
            */
            class Test
            {
               /**
                * 默认方法.
                */
               public function handle(): array
               {
                   return ['hello' => 'world'];
               }
            }
            EOT;
    }

    #[Api([
        'zh-CN:title' => '自动 restful 路由',
        'zh-CN:description' => <<<'EOT'
Restful 已经是一种开发主流，前后端分离的场景我们通常会定义 Restful 路由来向前端提供接口服务。

我们访问同一个 URL 的时候,根据不同的`请求类型`访问不同的后台。

路径 | 请求类型 |  匹配控制器 |  备注
--     | ---    | ---                               |--
/car   | GET    | App\Controller\Car::index()   | 没有参数则请求列表
/car/5 | GET    | App\Controller\Car::show()    |
/car/5 | POST   | App\Controller\Car::store()   |
/car/5 | DELETE | App\Controller\Car::destroy() |
/car/5 | PUT    | App\Controller\Car::update()  |
/car/5/otherGet | GET    | App\Controller\Car::otherGet()  |
/car/5/otherPost | POST    | App\Controller\Car::otherPost()  |

路由系统会分析 pathInfo，系统会首先尝试正则匹配 Restful 风格，否则执行传统 `MVC` 匹配。

Restful 风格路由如果匹配成功，如果这个时候没有方法，系统根据请求类型自动补全方法完成 Restful 请求.

我们可以通过 Request 中的 attributes 来访问参数中的 Restful 资源 ID。

``` php
(int) \Leevel::make('request')->attributes->get(\Leevel\Router\IRouter::RESTFUL_ID);
```
EOT,
    ])]
    public function doc4(): void
    {
    }

    #[Api([
        'zh-CN:title' => 'PHP 8 注解路由',
        'zh-CN:description' => <<<'EOT'
上面是一种预热，我们的框架路由设计是这样，优先进行 `pathInfo` 解析，如果解析失败将进入注解路由高级解析阶段。

### 基础

路径  |  匹配控制器 |  备注
--|---|--
http://127.0.0.1:9527/api/v1/demo/liu | 路由  | 注解路由

访问 `http://127.0.0.1:9527/api/v1/demo/liu`:

```
Hi you, you name is liu in version 1
```

QueryPHP 采用 PHP 8 属性来定义注解路由，需要定义 `Route` 注解路由。。
EOT,
    ])]
    public function doc5(): void
    {
        <<<'EOT'
            use Leevel\Router\Route;
            #[Route(
                path: "/api/v1/demo/{name:[A-Za-z]+}/",
                attributes: ["args1" => "hello", "args2" => "world"],
            )]
            public function demo1(string $name): string
            {
                return sprintf('Hi you, you name is %s in version 1', $name);
            }
            EOT;
    }

    #[Api([
        'zh-CN:description' => <<<'EOT'
VS Laravel:

``` php
Route::get('/', function () {
});
```

### 单条路由

系统支持一些自定义属性，可以扩展看路由的功能。

```
attributes: ["args1" => "hello", "args2" => "world"],
bind: "\\App\\App\\Controller\\Petstore\\Pet@withBind",
domain: "{subdomain:[A-Za-z]+}-vip.{domain}",
middlewares: "api",
port: "9527",
scheme: "https",
```

::: tip
 * 参数 bind 未设置，系统会自动绑定当前注释的控制器方法
 * 参数 bind 默认会绑定到类的 `handle` 方法，`@` 可以则可以自定义绑定方法
:::

路由地址 path 和域名支持正则参数

```
/api/v1/petLeevelForApi/{petId:[A-Za-z]+}/
{subdomain:[A-Za-z]+}-vip.{domain}
```
EOT,
    ])]
    public function doc6(): void
    {
    }

    #[Api([
        'zh-CN:title' => '结束语',
        'zh-CN:description' => <<<'EOT'
路由基本介绍完了，主要由两种风格的路由构成。
EOT,
    ])]
    public function doc7(): void
    {
    }
}
