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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Debug\Middleware;

use Closure;
use Leevel\Debug\Debug as Debugs;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Kernel\IApp;

/**
 * Debug 中间件.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.20
 *
 * @version 1.0
 */
class Debug
{
    /**
     * 应用管理.
     *
     * @var \Leevel\Kernel\IApp
     */
    protected IApp $app;

    /**
     * debug 管理.
     *
     * @var \Leevel\Debug\Debug
     */
    protected Debugs $debug;

    /**
     * 构造函数.
     *
     * @param \Leevel\Kernel\IApp $app
     * @param \Leevel\Debug\Debug $debug
     */
    public function __construct(IApp $app, Debugs $debug)
    {
        $this->app = $app;
        $this->debug = $debug;
    }

    /**
     * 请求.
     *
     * @param \Closure              $next
     * @param \Leevel\Http\IRequest $request
     */
    public function handle(Closure $next, IRequest $request): void
    {
        if (!$this->app->debug()) {
            $next($request);

            return;
        }

        $this->debug->bootstrap();

        $next($request);
    }

    /**
     * 响应.
     *
     * @param \Closure               $next
     * @param \Leevel\Http\IRequest  $request
     * @param \Leevel\Http\IResponse $response
     */
    public function terminate(Closure $next, IRequest $request, IResponse $response): void
    {
        if (!$this->app->debug()) {
            $next($request, $response);

            return;
        }

        $this->debug->handle($request, $response);

        $next($request, $response);
    }
}
