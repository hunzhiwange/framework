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

use Leevel\Http\IRequest;
use Leevel\Router\IRouter;

/**
 * pathInfo 路由匹配.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.15
 *
 * @version 1.0
 */
class PathInfo extends Match implements IMatch
{
    /**
     * 匹配数据项.
     *
     * @param \Leevel\Router\IRouter $router
     * @param \Leevel\Http\IRequest  $request
     *
     * @return array
     */
    public function matche(IRouter $router, IRequest $request): array
    {
        $this->request = $request;
        $this->router = $router;

        $pathInfo = $this->getPathInfo();
        $result = [];

        // 匹配基础路径
        $middlewares = $this->matcheBasePaths($pathInfo);

        // 匹配分组路径
        list($pathInfo, $middlewares) = $this->matcheGroupPaths($pathInfo, $middlewares);

        $result[IRouter::MIDDLEWARES] = $middlewares;

        $pathInfo = trim($pathInfo, '/');
        $paths = $pathInfo ? explode('/', $pathInfo) : [];

        // 应用
        if ($paths && $this->isFindApp($paths[0])) {
            $result[IRouter::APP] = substr(array_shift($paths), 1);
        }

        list($paths, $params) = $this->normalizePathsAndParams($paths);

        if (!$paths) {
            $result[IRouter::CONTROLLER] = IRouter::DEFAULT_CONTROLLER;

            return $result;
        }

        if (1 === count($paths)) {
            $result[IRouter::CONTROLLER] = array_pop($paths);
        } else {
            if ($paths) {
                $result[IRouter::ACTION] = array_pop($paths);
            }

            if ($paths) {
                $result[IRouter::CONTROLLER] = array_pop($paths);
            }

            if ($paths) {
                $result[IRouter::PREFIX] = $paths;
            }
        }

        $result[IRouter::PARAMS] = array_merge($result[IRouter::PARAMS] ?? [], $params);

        return $result;
    }

    /**
     * 是否找到 app.
     *
     * @param string $path
     *
     * @return bool
     */
    protected function isFindApp(string $path): bool
    {
        return 0 === strpos($path, ':');
    }

    /**
     * 解析路径和参数.
     *
     * @param array $data
     *
     * @return array
     */
    protected function normalizePathsAndParams(array $data): array
    {
        $paths = $params = [];

        $k = 0;

        foreach ($data as $item) {
            if (is_numeric($item)) {
                $params['_param'.$k] = $item;
                $k++;
            } else {
                $paths[] = $item;
            }
        }

        return [
            $paths,
            $params,
        ];
    }
}
