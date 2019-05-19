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

namespace Leevel\Log\Middleware;

use Closure;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Log\Manager;

/**
 * Log 中间件.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.11.14
 *
 * @version 1.0
 */
class Log
{
    /**
     * log 管理.
     *
     * @var \Leevel\Log\Manager
     */
    protected Manager $manager;

    /**
     * 构造函数.
     *
     * @param \Leevel\Log\Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
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
