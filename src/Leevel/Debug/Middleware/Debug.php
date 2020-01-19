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

namespace Leevel\Debug\Middleware;

use Closure;
use Leevel\Debug\Debug as Debugs;
use Leevel\Http\Request;
use Leevel\Http\Response;
use Leevel\Kernel\IApp;

/**
 * Debug 中间件.
 */
class Debug
{
    /**
     * 应用.
     *
     * @var \Leevel\Kernel\IApp
     */
    protected IApp $app;

    /**
     * Debug 管理.
     *
     * @var \Leevel\Debug\Debug
     */
    protected Debugs $debug;

    /**
     * 构造函数.
     */
    public function __construct(IApp $app, Debugs $debug)
    {
        $this->app = $app;
        $this->debug = $debug;
    }

    /**
     * 请求.
     */
    public function handle(Closure $next, Request $request): void
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
     */
    public function terminate(Closure $next, Request $request, Response $response): void
    {
        if (!$this->app->debug()) {
            $next($request, $response);

            return;
        }

        $this->debug->handle($request, $response);

        $next($request, $response);
    }
}
