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
use Leevel\Router\Router;
use Leevel\Router\IRouter;
use Leevel\Console\Cli as ConsoleCli;

/**
 * 路由命令行匹配.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.15
 *
 * @version 1.0
 */
class Cli implements IMatch
{
    /**
     * 匹配路径.
     *
     * @param \Leevel\Router\Router $router
     * @param \Leevel\Http\Request  $request
     *
     * @return array
     */
    public function matche(Router $router, Request $request): array
    {
        list($node, $querys, $options) = (new ConsoleCli())->parse();

        $result = [];

        if ($node) {
            $result = $router->matchePath($node);
        }

        if ($querys) {
            $result = array_merge($result, $querys);
        }

        $result[IRouter::PARAMS] = $options;

        return $result;
    }
}
