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

namespace Leevel\Debug\Middleware;

use Closure;
use Leevel\Debug\Debug as Debugs;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Kernel\IProject;

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
     * 项目管理.
     *
     * @var \Leevel\Kernel\IProject
     */
    protected $project;

    /**
     * debug 管理.
     *
     * @var \Leevel\Debug\Debug
     */
    protected $debug;

    /**
     * 构造函数.
     *
     * @param \Leevel\Kernel\IProject $project
     * @param \Leevel\Debug\Debug     $debug
     */
    public function __construct(IProject $project, Debugs $debug)
    {
        $this->project = $project;
        $this->debug = $debug;
    }

    /**
     * 响应.
     *
     * @param \Closure               $next
     * @param \Leevel\Http\IRequest  $request
     * @param \Leevel\Http\IResponse $response
     */
    public function terminate(Closure $next, IRequest $request, IResponse $response)
    {
        if (!$this->project->debug()) {
            return $next($request, $response);
        }

        $this->debug->handle($request, $response);

        $next($request, $response);
    }
}
