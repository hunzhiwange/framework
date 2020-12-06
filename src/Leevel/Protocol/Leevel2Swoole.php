<?php

declare(strict_types=1);

namespace Leevel\Protocol;

use Swoole\Http\Response as SwooleHttpResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Leevel 规范请求转 Swoole.
 */
class Leevel2Swoole
{
    /**
     * 从 Leevel 响应对象创建 Swoole 响应对象.
     */
    public function createResponse(Response $response, SwooleHttpResponse $swooleResponse): SwooleHttpResponse
    {
        $this->convertRedirectTargetUrl($response, $swooleResponse);
        $this->convertHeaders($response, $swooleResponse);
        $swooleResponse->status($response->getStatusCode());
        $swooleResponse->write($response->getContent() ?: ' ');

        return $swooleResponse;
    }

    /**
     * 转换跳转地址.
     */
    protected function convertRedirectTargetUrl(Response $response, SwooleHttpResponse $swooleResponse): void
    {
        if ($response instanceof RedirectResponse &&
            method_exists($swooleResponse, 'redirect')) {
            $swooleResponse->redirect($response->getTargetUrl());
        }
    }

    /**
     * 转换响应头.
     */
    protected function convertHeaders(Response $response, SwooleHttpResponse $swooleResponse): void
    {
        foreach ($response->headers->all() as $name => $values) {
            $name = ucwords($name, '-');
            foreach ($values as $value) {
                $swooleResponse->header($name, $value);
            }
        }
    }
}
