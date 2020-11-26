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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Router;

/**
 * @api(
 *     title="Summary",
 *     zh-CN:title="概述",
 *     zh-TW:title="概述",
 *     path="router/README",
 *     description="
 * 对于一个框架来说路由是一件非常重要的事情，可以说是框架的核心之一。路由的使用便捷和理解复杂度以及性能对整个框架来说至关重要。
 * 
 * ## 更新日志
 * 
 * |版本|描述|
 * |:-|:-|
 * |1.1.0-alpha.2|废弃掉使用 Swagger-php 作为路由数据提供方，转为采用 PHP 8 属性来定义注解路由。|
 * ",
 *     zh-CN:description="
 * 对于一个框架来说路由是一件非常重要的事情，可以说是框架的核心之一。路由的使用便捷和理解复杂度以及性能对整个框架来说至关重要。
 * 
 * ## 更新日志
 * 
 * |版本|描述|
 * |:-|:-|
 * |1.1.0-alpha.2|废弃掉使用 Swagger-php 作为路由数据提供方，转为采用 PHP 8 属性来定义注解路由。|
 * ",
 *     zh-TW:description="
 * 对于一个框架来说路由是一件非常重要的事情，可以说是框架的核心之一。路由的使用便捷和理解复杂度以及性能对整个框架来说至关重要。
 * 
 * ## 更新日志
 * 
 * |版本|描述|
 * |:-|:-|
 * |1.1.0-alpha.2|废弃掉使用 Swagger-php 作为路由数据提供方，转为采用 PHP 8 属性来定义注解路由。|
 * ",
 * )
 */
class SummaryDoc
{
    /**
     * @api(
     *     zh-CN:title="路由解析",
     *     zh-CN:description="
     * QueryPHP 有一个非常独特的地方就是路由设计与其它框架有点出入，我们不需要像 Laravel 5、Thinkphp 5 等框架一样定义路由。
     *
     * ### Laravel 5
     *
     * ``` php
     * Route::middleware(['first', 'second'])->group(function () {
     *     Route::get('/', function () {
     *     });
     *     Route::get('user/profile', function () {
     *     });
     * });
     * ```
     *
     * ### ThinkPHP 5
     *
     * ``` php
     * Route::group('blog', function () {
     *     Route::rule(':id', 'blog/read');
     *     Route::rule(':name', 'blog/read');
     * })->ext('html')->pattern(['id' => '\d+', 'name' => '\w+']);
     * ```
     *
     * ### FastRoute
     *
     * ``` php
     * $r->addRoute('GET', '/user/{id:\d+}', 'handler');
     * $r->addRoute('GET', '/user/{name}', 'handler');
     * $r->addRoute('GET', '/user/{name:.+}', 'handler');
     * ```
     *
     * ::: tip
     * 其中 FastRoute 中提供一个路由 10 条路由合并匹配算法非常地高效，QueryPHP 已经吸收。 [合并路由匹配算法](http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html)
     * :::
     *
     * ### Swagger-php 注解路由
     *
     * 在工作中我们大量使用 Swagger-php 来生成 API 文档来定义后台的数据结构，这样子前端和后台也可以同时进行，最后
     * 可以在一起进行联调。
     *
     * 随着时间的推移，我们发现 Swagger-php 生成的 OpenApi 规范的数据结构就是一个标准的路由系统。我像可不可以直接
     * 在这个基础上加入自定义的标签实现整个框架的路由呢，经过不断的完善终于搞定，后面会接着讲。
     * 
     * QueryPHP 框架提供 `MVC 自动路由` 并能够智能解析 Restful 请求和基于 OpenApi 3.0 规范的 Swagger-php 注解
     * 路由，文档路由一步搞定。所以我们不需要定义一个 router.php 来注册我们的路由，写好文档路由就自动生成好了，并且
     * 能够缓存起来加速解析。
     * ",
     *     zh-CN:note="从 1.1.0-alpha.2 开始采用 PHP 8 属性来定义注解路由。",
     *     lang="",
     * )
     */
    public function doc1(): void
    {
    }

    /**
     * @api(
     *     zh-CN:title="路由匹配过程",
     *     zh-CN:description="
     * QueryPHP 会优先进行`MVC 自动路由`匹配，也包含 `Restful` 路由匹配,如果匹配失败就会进行注解路由匹配阶段，如果还是
     * 匹配失败则会抛出一个路由无法找到的异常。
     * ",
     *     zh-CN:note="",
     *     lang="",
     * )
     */
    public function doc2(): void
    {
    }

    /**
     * @api(
     *     zh-CN:title="自动 MVC 路由",
     *     zh-CN:description="
     * 很多时候我们不是特别关心它是 GET、POST 还是 Put，我们就想简单输入一个地址就可以访问到我们的控制器。
     *
     * 路径  |  匹配控制器 |  备注
     * --|---|--
     * /  | App\App\Controller\Home::index()  |
     * /controller/action  | App\App\Controller\Controller::action()  |
     * /:blog/controller/action | Blog\App\Controller\Controller::action()  |  `:` 表示应用
     * /dir1/dir2/dir3/controller/action |  App\App\Controller\Dir1\Dir2\Dir3\Controller::action() |
     * /he_llo-wor/Bar/foo/xYY-ac/controller_xx-yy/action-xxx_Yzs  | App\App\Controller\HeLloWor\Bar\Foo\XYYAc\ControllerXxYy::actionXxxYzs()  |
     *
     * ::: warning
     * 如果方法单独成为一个类，则对应的请求入口为 `handle`，我们推荐为每一个方法定义一个类，避免与其它方法冲突，而且路由匹配性能最佳。
     * 框架底层会优先匹配方法单独成类，匹配失败则匹配控制器类对应方法操作，如果还是匹配失败进入注解路由匹配阶段。
     * :::
     *
     * 例如访问地址 `http://queryphp.cn/api/test`:
     *
     * ",
     *    note="上面这种就是一个简单粗暴的路由，简单有效，大多数时候可以满足我们系统开发的需求。",
     *    lang="php",
     * )
     */
    public function doc3(): void
    {
        <<<'EOT'
            <?php

            declare(strict_types=1);
            
            /*
            * This file is part of the forcodepoem package.
            *
            * The PHP Application Created By Code Poem. <Query Yet Simple>
            * (c) 2018-2099 http://forcodepoem.com All rights reserved.
            *
            * For the full copyright and license information, please view the LICENSE
            * file that was distributed with this source code.
            */
            
            namespace App\App\Controller\Api;
            
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

    /**
     * @api(
     *     zh-CN:title="自动 restful 路由",
     *     zh-CN:description="
     * Restful 已经是一种开发主流，前后端分离的场景我们通常会定义 Restful 路由来向前端提供接口服务。
     *
     * 我们访问同一个 URL 的时候,根据不同的`请求类型`访问不同的后台。
     *
     * 路径 | 请求类型 |  匹配控制器 |  备注
     * --     | ---    | ---                               |--
     * /car   | GET    | App\App\Controller\Car::index()   | 没有参数则请求列表
     * /car/5 | GET    | App\App\Controller\Car::show()    |
     * /car/5 | POST   | App\App\Controller\Car::store()   |
     * /car/5 | DELETE | App\App\Controller\Car::destroy() |
     * /car/5 | PUT    | App\App\Controller\Car::update()  |
     * /car/5/otherGet | GET    | App\App\Controller\Car::otherGet()  |
     * /car/5/otherPost | POST    | App\App\Controller\Car::otherPost()  |
     *
     * 路由系统会分析 pathInfo，系统会首先尝试正则匹配 Restful 风格，否则执行传统 `MVC` 匹配。
     *
     * Restful 风格路由如果匹配成功，如果这个时候没有方法，系统根据请求类型自动补全方法完成 Restful 请求.
     *
     * 我们可以通过 Request 中的 attributes 来访问参数中的 Restful 资源 ID。
     *
     * ``` php
     * (int) \Leevel::make('request')->attributes->get(\Leevel\Router\IRouter::RESTFUL_ID);
     * ```
     * ",
     *    note="",
     * )
     */
    public function doc4(): void
    {
    }

    /**
     * @api(
     *     zh-CN:title="Swagger PHP 注解路由",
     *     zh-CN:description="
     * 上面是一种预热，我们的框架路由设计是这样，优先进行 `pathInfo` 解析，如果解析失败将进入注解路由高级解析阶段。
     *
     * ### 基础
     *
     * 路径  |  匹配控制器 |  备注
     * --|---|--
     * http://127.0.0.1:9527/api  | OpenApi 3 JSON  | JSON 结构
     * http://127.0.0.1:9527/apis/  | Swagger-ui  | Swagger-ui 入口
     * http://127.0.0.1:9527/api/v1/petLeevelForApi/helloworld | 路由  | 注解路由
     *
     * 访问 `http://127.0.0.1:9527/api/v1/petLeevelForApi/helloworld`:
     *
     * ```
     * Hi you,i am petLeevelForApi and it petId is helloworld
     * ```
     *
     * 在工作大量使用 Swagger-php 来生成注释文档,它其实是一个标准的路由。
     * ",
     *    note="从 1.1.0-alpha.2 开始采用 PHP 8 属性来定义注解路由，需要定义 `Route` 注解路由。",
     * )
     */
    public function doc5(): void
    {
        <<<'EOT'
            /**
             * @OA\Get(
             *     path="/api/v1/petLeevelForApi/{petId:[A-Za-z]+}/",
             *     tags={"pet"},
             *     summary="Just test the router",
             *     operationId="petLeevelForApi",
             *     @OA\Parameter(
             *         in="path",
             *         description="ID of pet to return",
             *         required=true,
             *         @OA\Schema(
             *             type="integer",
             *             format="int64"
             *         )
             *     ),
             *     @OA\Response(
             *         response=405,
             *         description="Invalid input"
             *     ),
             *     security={
             *         {"petstore_auth": {"write:pets", "read:pets"}}
             *     },
             *     leevelAttributes={"args1": "hello", "args2": "world"}
             * )
             */
            #[Route(
                path: "/api/v1/petLeevelForApi/{petId:[A-Za-z]+}/",
                attributes: ["args1" => "hello", "args2" => "world"],
            )]
            public function petLeevelForApi(string $petId): string
            {
                return sprintf('Hi you,i am petLeevelForApi and it petId is %s', $petId);
            }
            EOT;
    }

    /**
     * @api(
     *     zh-CN:title="",
     *     zh-CN:description="
     * VS Laravel:
     *
     * ``` php
     * Route::get('/', function () {
     * });
     * ```
     *
     * 看起来我们的路由复杂很多，实际上我们只是定义 `Leevel` 开头的属性才是我们的扩展配置。
     * QueryPHP 的注解路由，在标准 Swagger-php 的基础上增加了自定义属性扩展功能。
     *
     * ### 单条路由
     *
     * 系统支持一些自定义属性，可以扩展看路由的功能。
     *
     * ```
     * leevelAttributes={"args1": "hello", "args2": "world"},
     * leevelBind="\\App\\App\\Controller\\Petstore\\Pet@withBind"
     * leevelDomain="{subdomain:[A-Za-z]+}-vip.{domain}",
     * leevelMiddlewares="api"
     * leevelPort="9527"
     * leevelScheme="https",
     * ```
     * 
     * 对应的 1.1.0-alpha.2 后的注解路由
     * 
     * ```
     * attributes: ["args1" => "hello", "args2" => "world"],
     * bind: "\\App\\App\\Controller\\Petstore\\Pet@withBind",
     * domain: "{subdomain:[A-Za-z]+}-vip.{domain}",
     * middlewares: "api",
     * port: "9527",
     * scheme: "https",
     * ```
     *
     * ::: danger
     *  * leevelBind 未设置自动绑定当前注释的控制器和方法
     *  * 文档注释未写到控制器上，这个时候没有上下文控制器，需要使用 leevelBind 绑定
     *  * leevelBind 未设置 `@` 则绑定到类的 `handle` 方法，`@` 可以自定义绑定方法
     * :::
     *
     * 路由地址 path 和域名支持正则参数
     *
     * ```
     * /api/v1/petLeevelForApi/{petId:[A-Za-z]+}/
     * {subdomain:[A-Za-z]+}-vip.{domain}
     * ```
     * ",
     *    note="",
     * )
     */
    public function doc6(): void
    {
    }

    /**
     * @api(
     *     zh-CN:title="结束语",
     *     zh-CN:description="
     * 路由基本介绍完了，主要由两种风格的路由构成。
     * ",
     *    note="",
     * )
     */
    public function doc7(): void
    {
    }
}
