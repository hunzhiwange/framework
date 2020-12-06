<?php

declare(strict_types=1);

namespace Leevel\Router;

use Leevel\Http\RedirectResponse;
use Leevel\Session\ISession;

/**
 * Redirect.
 */
class Redirect
{
    /**
     * SESSION 仓储.
     */
    protected ISession $session;

    /**
     * 构造函数.
     */
    public function __construct(protected IUrl $url)
    {
    }

    /**
     * 返回一个 URL 生成跳转响应.
     */
    public function url(string $url, array $params = [], string $subdomain = 'www', null|bool|string $suffix = null, int $status = 302, array $headers = []): RedirectResponse
    {
        $url = $this->url->make($url, $params, $subdomain, $suffix);

        return $this->createRedirectResponse($url, $status, $headers);
    }

    /**
     * 返回一个跳转响应.
     */
    public function raw(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        return $this->createRedirectResponse($url, $status, $headers);
    }

    /**
     * 取回 URL 生成实例.
     */
    public function getUrl(): IUrl
    {
        return $this->url;
    }

    /**
     * 设置 SESSION 仓储.
     */
    public function setSession(ISession $session): void
    {
        $this->session = $session;
    }

    /**
     * 返回一个跳转响应.
     */
    protected function createRedirectResponse(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        $redirect = new RedirectResponse($url, $status, $headers);
        if (isset($this->session)) {
            $redirect->setSession($this->session);
        }

        return $redirect;
    }
}
