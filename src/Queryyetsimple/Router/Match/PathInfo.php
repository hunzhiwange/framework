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

namespace Leevel\Router\Match;

use Leevel\Http\Request;
use Leevel\Router\IRouter;
use Leevel\Router\Router;

/**
 * 路由 pathInfo 匹配.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.15
 *
 * @version 1.0
 */
class PathInfo implements IMatch
{
    /**
     * Router.
     *
     * @var \Leevel\Router\Router
     */
    protected $router;

    /**
     * HTTP Request.
     *
     * @var \Leevel\Http\Request
     */
    protected $request;

    /**
     * 匹配数据项.
     *
     * @param \Leevel\Router\Router $router
     * @param \Leevel\Http\Request  $request
     *
     * @return array
     */
    public function matche(Router $router, Request $request): array
    {
        $pathInfo = $request->getPathInfo();
        $pathInfo = trim($pathInfo, '/');

        // 首页
        if (!$pathInfo) {
            return [
                IRouter::CONTROLLER => IRouter::DEFAULT_HOME_CONTROLLER,
                IRouter::ACTION     => IRouter::DEFAULT_HOME_ACTION,
            ];
        }

        $pathInfo = '/'.$pathInfo;

        return $router->matchePath($pathInfo);
    }
}
