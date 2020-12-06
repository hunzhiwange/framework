<?php

declare(strict_types=1);

namespace Leevel\Router;

use Leevel\Http\Request;

/**
 * IUrl 生成.
 */
interface IUrl
{
    /**
     * 生成路由地址.
     */
    public function make(string $url, array $params = [], string $subdomain = 'www', null|bool|string $suffix = null): string;

    /**
     * 返回 HTTP 请求.
     */
    public function getRequest(): Request;

    /**
     * 获取域名.
     */
    public function getDomain(): string;
}
