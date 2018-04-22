<?php
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
namespace Leevel\Router;

use Leevel\Di\Provider;

/**
 * 路由服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.04.17
 * @version 1.0
 */
abstract class RouterProvider extends Provider
{
    /**
     * 控制器相对目录
     * 
     * @var string
     */
    protected $controllerDir;

    /**
     * 中间件分组
     * 分组可以很方便地批量调用组件
     *
     * @var array
     */
    protected $middlewareGroups;

    /**
     * 中间件别名
     * HTTP 中间件提供一个方便的机制来过滤进入应用程序的 HTTP 请求
     * 例外在应用执行结束后响应环节也会调用 HTTP 中间件
     *
     * @var array
     */
    protected $middlewareAlias;
    
    /**
     * bootstrap
     *
     * @return void
     */
    public function bootstrap()
    {
        $this->setControllerDir();

        $this->setMiddleware();

        if ($this->isRouterCached()) {
            $this->importCachedRouters();
        } else {
            $this->loadRouters();
        }
    }
    
    /**
     * 注册一个提供者
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * 返回路由
     * 
     * @return array
     */
    public function getRouters() {
        return [];
    }

    /**
     * 全局中间件
     *
     * @return array
     */
    public function getMiddlewares() {
        return [];
    }

    /**
     * 导入路由缓存
     *
     * @return void
     */
    protected function importCachedRouters()
    {
        $routers = include $this->getRouterCachePath();

        $this->setRoutersData($routers);
    }

    /**
     * 注册路由
     *
     * @return void
     */
    protected function loadRouters()
    {
        $routers = $this->getRouters();

        $middlewares = $this->getMiddlewares();
        $middlewares = $this->makeMiddlewareParser()->handle($middlewares);

        $this->setGlobalMiddlewares($middlewares);

        $this->setRoutersData($routers);
    }

    /**
     * 生成中间件分析器
     * 
     * @return \Leevel\Router\MiddlewareParser
     */
    protected function makeMiddlewareParser() {
        return new MiddlewareParser($this->container['router']);
    }

    /**
     * 设置全局中间件数据
     *
     * @param array $middlewares
     * @return void
     */
    protected function setGlobalMiddlewares(array $middlewares)
    {
        $this->container['router']->setGlobalMiddlewares($middlewares);
    }

    /**
     * 设置路由数据
     *
     * @param array $routers
     * @return void
     */
    protected function setRoutersData(array $routers)
    {
        $this->container['router']->setBasepaths($routers['basepaths']);
        $this->container['router']->setGroups($routers['groups']);
        $this->container['router']->setRouters($routers['routers']);
    }

    /**
     * 路由是否已经缓存
     *
     * @return boolean
     */
    protected function isRouterCached()
    {
        return file_exists($this->getRouterCachePath());
    }

    /**
     * 获取路由缓存地址
     *
     * @return string
     */
    protected function getRouterCachePath()
    {
        return path_router_cache();
    }

    /**
     * 设置应用控制器目录
     *
     * @return void
     */
    protected function setControllerDir()
    {
        if (! is_null($this->controllerDir)) {
            $this->container['router']->setControllerDir($this->controllerDir);
        }
    }

    /**
     * 设置中间件 
     *
     * @return void
     */
    protected function setMiddleware()
    {
        if (! is_null($this->middlewareGroups)) {
            $this->container['router']->setMiddlewareGroups($this->middlewareGroups);
        }

        if (! is_null($this->middlewareAlias)) {
            $this->container['router']->setMiddlewareAlias($this->middlewareAlias);
        }
    }
}
