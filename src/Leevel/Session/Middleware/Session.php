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

namespace Leevel\Session\Middleware;

use Closure;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
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
    protected Manager $manager;

    /**
     * 构造函数.
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * 请求
     */
    public function handle(Closure $next, IRequest $request): void
    {
        $this->startSession($request);

        $next($request);
    }

    /**
     * 响应.
     */
    public function terminate(Closure $next, IRequest $request, IResponse $response): void
    {
        $this->setPrevUrl($request);
        $this->saveSession();

        if (!$this->getSessionId($request)) {
            $response->setCookie(
                $this->manager->getName(),
                $this->manager->getId(),
                ['expire' => $this->getSessionExpire()]
            );
        }

        $next($request, $response);
    }

    /**
     * 启动 session.
     */
    protected function startSession(IRequest $request): void
    {
        $this->manager->start($this->getSessionId($request));
    }

    /**
     * 保存 session.
     */
    protected function saveSession(): void
    {
        $this->manager->save();
    }

    /**
     * 保存当期请求 URL.
     */
    protected function setPrevUrl(IRequest $request): void
    {
        $this->manager->setPrevUrl($request->getUri());
    }

    /**
     * 获取 session ID.
     */
    protected function getSessionId(IRequest $request): ?string
    {
        return $request->cookies->get(
            $this->manager->getName(), null
        );
    }

    /**
     * 获取 session 过期时间.
     */
    protected function getSessionExpire(): int
    {
        return $this->manager->getSessionOption()['expire'] ?? 0;
    }
}
