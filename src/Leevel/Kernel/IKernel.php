<?php

declare(strict_types=1);

namespace Leevel\Kernel;

use Leevel\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 内核执行接口.
 */
interface IKernel
{
    /**
     * 响应 HTTP 请求.
     */
    public function handle(Request $request): Response;

    /**
     * 执行结束.
     */
    public function terminate(Request $request, Response $response): void;

    /**
     * 初始化.
     */
    public function bootstrap(): void;

    /**
     * 返回应用.
     */
    public function getApp(): IApp;
}
