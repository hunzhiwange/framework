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

namespace Leevel\Log\Middleware;

use Closure;
use Leevel\Http\Request;
use Leevel\Log\Manager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Log 中间件.
 */
class Log
{
    /**
     * 日志管理器.
     *
     * @var \Leevel\Log\Manager
     */
    protected Manager $manager;

    /**
     * 构造函数.
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * 响应.
     */
    public function terminate(Closure $next, Request $request, Response $response): void
    {
        $this->saveLog();
        $next($request, $response);
    }

    /**
     * 保存日志.
     */
    protected function saveLog(): void
    {
        $this->manager->flush();
    }
}
