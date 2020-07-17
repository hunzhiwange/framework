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

namespace Leevel\Router;

use Leevel\Kernel\Proxy\App;
use Leevel\Router\Proxy\Url;

/**
 * OpenAPI 路由扫描.
 */
class ScanRouter
{
    /**
     * OpenAPI 路由分析.
     *
     * @var \Leevel\Router\OpenApiRouter
     */
    protected OpenApiRouter $openApiRouter;

    /**
     * 构造函数.
     */
    public function __construct(MiddlewareParser $middlewareParser, array $basePaths = [], array $groups = [])
    {
        $this->openApiRouter = new OpenApiRouter(
            $middlewareParser,
            $this->getDomain(),
            $basePaths,
            $groups
        );

        foreach ([$this->routePath(), $this->appPath()] as $path) {
            $this->openApiRouter->addScandir($path);
        }
    }

    /**
     * 响应.
     */
    public function handle(): array
    {
        return $this->openApiRouter->handle();
    }

    /**
     * 获取顶级域名.
     */
    protected function getDomain(): string
    {
        return Url::getDomain();
    }

    /**
     * 获取应用目录.
     */
    protected function appPath(): string
    {
        return App::appPath();
    }

    /**
     * 获取路由目录.
     */
    protected function routePath(): string
    {
        return App::path('router');
    }
}
