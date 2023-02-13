<?php

declare(strict_types=1);

namespace Leevel\Http;

use Leevel\Session\ISession;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

/**
 * Redirect 响应请求.
 */
class RedirectResponse extends SymfonyRedirectResponse
{
    use BaseResponse;

    /**
     * 错误键.
     */
    public const ERRORS_KEY = ':errors_key';

    /**
     * SESSION 仓储.
     */
    protected ?ISession $session = null;

    /**
     * 闪存一个数据片段到 SESSION.
     */
    public function with(array|string $key, mixed $value = null): void
    {
        $key = \is_array($key) ? $key : [$key => $value];
        foreach ($key as $k => $v) {
            // @phpstan-ignore-next-line
            $this->session->flash($k, $v);
        }
    }

    /**
     * 闪存错误信息.
     */
    public function withErrors(array|string $key, mixed $value = null): void
    {
        $key = \is_array($key) ? $key : [$key => $value];

        /** @phpstan-ignore-next-line */
        $errors = $this->session->getFlash(self::ERRORS_KEY, []);
        foreach ($key as $k => $v) {
            $errors[$k] = $v;
        }
        // @phpstan-ignore-next-line
        $this->session->flash(self::ERRORS_KEY, $errors);
    }

    /**
     * 获取 SESSION 仓储.
     */
    public function getSession(): ?ISession
    {
        return $this->session;
    }

    /**
     * 设置 SESSION 仓储.
     */
    public function setSession(ISession $session): void
    {
        $this->session = $session;
    }
}
