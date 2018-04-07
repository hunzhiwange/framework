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
 * 路由默认匹配
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.02.19
 * @version 1.0
 */
class Defaults
{

    /**
     * 匹配路径
     *
     * @param \Leevel\Router\Router $route
     * @param \Leevel\Http\Request $request
     * @return array
     */
    public function matche(Router $router, Request $request)
    { 
        $result = [];

        foreach ([Router::APP, Router::CONTROLLER, Router::ACTION, Router::PARAMS, Router::PREFIX] as $item) {
            if (isset($_GET[$item])) {
                if ($item == Router::PARAMS && ! is_array($_GET[$item])) {
                    $result[$item] = [
                        $_GET[$item]
                    ];
                } else {
                    $result[$item] = $_GET[$item];
                }
            }
        }

        return $result;
    }
}
