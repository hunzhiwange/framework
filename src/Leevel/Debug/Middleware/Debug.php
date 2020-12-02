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
use Leevel\Kernel\IApp;
use Symfony\Component\HttpFoundation\Response;

/**
 * Debug 中间件.
 */
class Debug
{
    /**
     * 构造函数.
     */
    public function __construct(protected IApp $app, protected Debugs $debug)
    {
    }

    /**
     * 请求.
     */
    public function handle(Closure $next, Request $request): void
    {
        if (!$this->app->isDebug()) {
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
        if (!$this->app->isDebug()) {
            $next($request, $response);

            return;
        }

        $this->debug->handle($request, $response);
        $next($request, $response);
    }
}
