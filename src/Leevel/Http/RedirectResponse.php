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
     *
     * @string
     */
    const ERRORS_KEY = ':errors_key';

    /**
     * SESSION 仓储.
     *
     * @var \Leevel\Session\ISession
     */
    protected ?ISession $session = null;

    /**
     * 闪存一个数据片段到 SESSION.
     *
     * @param array|string $key
     * @param mixed        $value
     */
    public function with($key, mixed $value = null): void
    {
        $key = is_array($key) ? $key : [$key => $value];
        foreach ($key as $k => $v) {
            $this->session->flash($k, $v);
        }
    }

    /**
     * 闪存错误信息.
     *
     * @param array|string $key
     * @param mixed        $value
     */
    public function withErrors($key, mixed $value = null): void
    {
        $key = is_array($key) ? $key : [$key => $value];
        $errors = $this->session->getFlash(self::ERRORS_KEY, []);
        foreach ($key as $k => $v) {
            $errors[$k] = $v;
        }
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
