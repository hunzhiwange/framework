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

namespace Leevel\Router;

use Leevel\Router;

/**
 * Swagger 路由扫描.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.04.18
 *
 * @version 1.0
 */
class ScanSwaggerRouter
{
    /**
     * swagger 路由分析.
     *
     * @var \Leevel\Router\SwaggerRouter
     */
    protected $swaggerRouter;

    /**
     * 构造函数.
     *
     * @param \Leevel\Router\MiddlewareParser $middlewareParser
     */
    public function __construct(MiddlewareParser $middlewareParser)
    {
        $this->swaggerRouter = new SwaggerRouter($middlewareParser, $this->getTopDomain(), $this->getController());

        // 添加扫描目录
        $this->swaggerRouter->addSwaggerScan($this->getApplicationDir());
    }

    /**
     * 响应.
     *
     * @return array
     */
    public function handle()
    {
        return $this->swaggerRouter->handle();
    }

    /**
     * 获取顶级域名.
     *
     * @return string
     */
    protected function getTopDomain()
    {
        return app()->make('option')->get('top_domain');
    }

    /**
     * 获取控制器.
     *
     * @return string
     */
    protected function getController()
    {
        return Router::getControllerDir();
    }

    /**
     * 获取应用目录.
     *
     * @param string $controller
     *
     * @return string
     */
    protected function getApplicationDir()
    {
        return path_application();
    }

    /**
     * 获取缓存路径.
     *
     * @return string
     */
    protected function getCachePath()
    {
        return app()->pathCacheRouterFile();
    }
}
