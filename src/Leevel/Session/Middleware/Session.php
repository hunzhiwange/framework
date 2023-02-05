<?php

declare(strict_types=1);

namespace Leevel\Session\Middleware;

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
     * 构造函数.
     */
    public function __construct(protected Manager $manager)
    {
    }

    /**
     * 请求.
     */
    public function handle(\Closure $next, Request $request): Response
    {
        $this->startSession($request);

        return $next($request);
    }

    /**
     * 响应.
     */
    public function terminate(\Closure $next, Request $request, Response $response): void
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
