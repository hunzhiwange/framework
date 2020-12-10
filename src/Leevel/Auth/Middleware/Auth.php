<?php

declare(strict_types=1);

namespace Leevel\Auth\Middleware;

use Closure;
use Leevel\Auth\AuthException;
use Leevel\Auth\Manager;
use Leevel\Http\Request;

/**
 * 认证中间件.
 */
class Auth
{
    /**
     * 构造函数.
     */
    public function __construct(protected Manager $manager)
    {
    }

    /**
     * 请求.
     *
     * @throws \Leevel\Auth\AuthException
     */
    public function handle(Closure $next, Request $request): void
    {
        if (!$this->manager->isLogin()) {
            throw new AuthException('User authorization failed.');
        }

        $next($request);
    }
}
