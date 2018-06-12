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

namespace Leevel\Session\Middleware;

use Closure;
use Leevel\Http\Request;
use Leevel\Http\Response;
use Leevel\Session\Manager;

/**
 * Session 中间件.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.11.14
 *
 * @version 1.0
 */
class Session
{
    /**
     * session 管理.
     *
     * @var \Leevel\Session\Manager
     */
    protected $manager;

    /**
     * 构造函数.
     *
     * @param \Leevel\Session\Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * 请求
     *
     * @param \Closure             $next
     * @param \Leevel\Http\Request $request
     */
    public function handle(Closure $next, Request $request)
    {
        $this->startSession();
        $next($request);
    }

    /**
     * 响应.
     *
     * @param \Closure              $next
     * @param \Leevel\Http\Request  $request
     * @param \Leevel\Http\Response $response
     */
    public function terminate(Closure $next, Request $request, Response $response)
    {
        $this->unregisterFlash();
        $this->setPrevUrl($request);
        $next($request, $response);
    }

    /**
     * 启动 session.
     */
    protected function startSession()
    {
        $this->manager->start();
    }

    /**
     * 清理闪存.
     */
    protected function unregisterFlash()
    {
        $this->manager->unregisterFlash();
    }

    /**
     * 保存当期请求 URL.
     *
     * @param \Leevel\Http\Request $request
     */
    protected function setPrevUrl(Request $request)
    {
        $this->manager->setPrevUrl($request->getUri());
    }
}
