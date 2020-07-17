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
use Leevel\Http\CookieUtils;
use Leevel\Http\Request;
use Leevel\Session\Manager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Session 中间件.
 */
class Session
{
    /**
     * Session 管理器.
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
     * 请求.
     */
    public function handle(Closure $next, Request $request): void
    {
        $this->startSession($request);
        $next($request);
    }

    /**
     * 响应.
     */
    public function terminate(Closure $next, Request $request, Response $response): void
    {
        if (!$this->manager->isStart()) {
            $next($request, $response);

            return;
        }

        $this->setPrevUrl($request);
        $this->saveSession();

        if (!$this->getSessionId($request)) {
            $response->headers->setCookie(
                CookieUtils::makeCookie(
                    $this->manager->getName(),
                    $this->manager->getId(),
                    ['expire' => $this->getSessionExpire()],
                )
            );
        }

        $next($request, $response);
    }

    /**
     * 启动 session.
     */
    protected function startSession(Request $request): void
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
    protected function setPrevUrl(Request $request): void
    {
        $this->manager->setPrevUrl($request->getUri());
    }

    /**
     * 获取 session ID.
     */
    protected function getSessionId(Request $request): ?string
    {
        return $request->cookies->get(
            $this->manager->getName(),
            null
        );
    }

    /**
     * 获取 session 过期时间.
     */
    protected function getSessionExpire(): int
    {
        return $this->manager->getSessionOption()['cookie_expire'] ?? 0;
    }
}
