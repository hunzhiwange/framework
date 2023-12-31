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
    protected ?ISession $session = null;

    /**
     * 返回一个跳转响应.
     */
    public function url(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        return $this->createRedirectResponse($url, $status, $headers);
    }

    /**
     * 设置 SESSION 仓储.
     */
    public function setSession(?ISession $session = null): void
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
