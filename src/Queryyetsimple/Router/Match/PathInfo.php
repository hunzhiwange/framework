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
namespace Leevel\Router\Match;

use Leevel\Http\Request;
use Leevel\Router\Router;

/**
 * 路由 pathInfo 匹配
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.02.15
 * @version 1.0
 */
class PathInfo
{

    /**
     * Router
     * 
     * @var \Leevel\Router\Router
     */
    protected $router;
    
    /** 
     * HTTP Request
     * 
     * @var \Leevel\Http\Request
     */
    protected $request;

    /**
     * 匹配数据项
     *
     * @param \Leevel\Router\Router $router
     * @param \Leevel\Http\Request $request
     * @return array
     */
    public function matche(Router $router, Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $pathInfo = trim($pathInfo, '/');

        // 首页
        if (! $pathInfo) {
            return [
                Router::CONTROLLER => Router::DEFAULT_HOME_CONTROLLER,
                Router::ACTION => Router::DEFAULT_HOME_ACTION
            ];
        }

        $pathInfo = '/' . $pathInfo;

        $result = [];

        $this->request = $request;
        $this->router = $router;

        // 匹配基础路径
        $basepaths = $this->router->getBasepaths();
        $basepath = '';
        foreach ($basepaths as $path) {
            if (strpos($pathInfo, $path) === 0) {
                $pathInfo = substr($pathInfo, strlen($path) + 1);
                $basepath = $path;
                break;
            }
        }

        $paths = explode('/', $pathInfo);

        // 应用
        if ($paths && $this->findApp($paths[0])) {
            $result[Router::APP] = substr(array_shift($paths), 1);
        }

        list($pathInfos, $options) = $this->parseOptionsAndPathInfos($paths);

        if (count($pathInfos) == 1) {
            $result[Router::CONTROLLER] = array_pop($pathInfos);
        } else { 
            if ($pathInfos) {
                $result[Router::ACTION] = array_pop($pathInfos);
            }

            if ($pathInfos) {
                $result[Router::CONTROLLER] = array_pop($pathInfos);
            }

            if ($pathInfos) {
                $result[Router::PREFIX] = implode('\\', array_map(function($item) {
                    if (strpos($item, '_') !== false) {
                        $item = str_replace('_', ' ', $item);
                        $item = str_replace(' ', '', ucwords($item));
                    } else {
                        $item = ucfirst($item);
                    }

                    return $item;
                },$pathInfos));
            }
        }

        $result[Router::PARAMS] = $options;

        $result[Router::PARAMS][Router::BASEPATH] = $basepath ?: null;

        return $result;
    }

    /**
     * 是否找到 app
     * 
     * @param string $app
     * @return boolean
     */
    protected function findApp($app)
    {
        return strpos($app, ':') === 0;
    }

    /**
     * 解析配置和 pathInfo
     * 
     * @param array $paths
     * @return array
     */
    protected function parseOptionsAndPathInfos(array $paths)
    {
        $pathInfos = $options = [];

        foreach ($paths as $path) {
            if (is_numeric($path)) {
                $options[] = $path;
            } else {
                $pathInfos[] = $path;
            }
        }

        return [
            $pathInfos,
            $options
        ];
    }
}
