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
namespace Queryyetsimple\Router\Match;

use Queryyetsimple\Http\Request;
use Queryyetsimple\Router\Router;

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
     * @var \Queryyetsimple\Router\Router
     */
    protected $router;
    
    /** 
     * HTTP Request
     * 
     * @var \Queryyetsimple\Http\Request
     */
    protected $request;

    /**
     * 匹配数据项
     *
     * @param \Queryyetsimple\Router\Router $route
     * @param \Queryyetsimple\Http\Request $request
     * @return array
     */
    public function matche(Router $router, Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $pathInfo = trim($pathInfo, '/');
        
        if (! $pathInfo) {
            return [];
        }

        $result = [];

        $this->request = $request;
        $this->router = $router;

        $paths = explode('/', $pathInfo);

        if ($paths && $this->findApp($paths[0])) {
            $result[Router::APP] = array_pop($paths);
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
                $result[Router::PREFIX] = implode('\\', $pathInfos);
            }
        }

        $result[Router::PARAMS] = $options;

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
        $apps = $this->router->getOption('apps');

        if (is_array($apps) && in_array($app, $apps)) {
            return true;
        }

        return false;
    }

    /**
     * 解析配置和 pathInfo
     * 
     * @param array $paths
     * @return array
     */
    protected function parseOptionsAndPathInfos(array $paths)
    {
        $protected = $this->router->getOption('args_protected');
        $regex = $this->router->getOption('args_regex');
        $strict = $this->router->getOption('args_strict');

        $pathInfos = $options = [];

        foreach ($paths as $path) {
            if (is_numeric($path) || in_array($path, $protected) || $this->matchArgs($path, $regex, $strict)) {
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

    /**
     * 是否匹配参数正则
     *
     * @param array $path
     * @param array $regex
     * @param bool $strict
     * @return boolean
     */
    protected function matchArgs($path, array $regex = [], bool $strict)
    {
        if (! $regex) {
            return false;
        }

        foreach ($regex as $item) {
            $item = sprintf('/^(%s)%s/', $item, $strict ? '$' : '');

            if (preg_match($item, $path, $mathes)) {
                return true;
            }
        }

        return false;
    }
}
