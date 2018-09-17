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

namespace Tests\Router\Middlewares;

use Closure;
use Leevel\Http\IRequest;

/**
 * demo3 中间件.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.13
 *
 * @version 1.0
 */
class Demo3
{
    public function __construct()
    {
    }

    public function handle(Closure $next, IRequest $request, int $arg1 = 1, string $arg2 = 'hello')
    {
        $GLOBALS['demo_middlewares'][] = sprintf('Demo3::handle(arg1:%s,arg2:%s)', $arg1, $arg2);

        $next($request);
    }
}
