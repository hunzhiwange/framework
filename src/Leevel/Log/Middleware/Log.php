<?php

declare(strict_types=1);

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
     * 构造函数.
     */
    public function __construct(protected Manager $manager)
    {
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
