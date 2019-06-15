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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Router;

use Leevel\Http\RedirectResponse;
use Leevel\Session\ISession;

/**
 * Redirect.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.03.02
 *
 * @version 1.0
 */
class Redirect
{
    /**
     * URL 生成实例.
     *
     * @var \Leevel\Router\Url
     */
    protected $url;

    /**
     * SESSION 仓储.
     *
     * @var \Leevel\Session\ISession
     */
    protected $session;

    /**
     * 构造函数.
     *
     * @param \Leevel\Router\Url $url
     */
    public function __construct(IUrl $url)
    {
        $this->url = $url;
    }

    /**
     * 返回一个 URL 生成跳转响应.
     *
     * @param string           $url
     * @param array            $params
     * @param string           $subdomain
     * @param null|bool|string $suffix
     * @param int              $status
     * @param array            $headers
     *
     * @return \Leevel\Http\RedirectResponse
     */
    public function url(string $url, array $params = [], string $subdomain = 'www', $suffix = null, int $status = 302, array $headers = []): RedirectResponse
    {
        $url = $this->url->make($url, $params, $subdomain, $suffix);

        return $this->createRedirectResponse($url, $status, $headers);
    }

    /**
     * 返回一个跳转响应.
     *
     * @param string $url
     * @param int    $status
     * @param array  $headers
     *
     * @return \Leevel\Http\RedirectResponse
     */
    public function raw(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        return $this->createRedirectResponse($url, $status, $headers);
    }

    /**
     * 取回 URL 生成实例.
     *
     * @return \Leevel\Router\IUrl
     */
    public function getUrl(): IUrl
    {
        return $this->url;
    }

    /**
     * 设置 SESSION 仓储.
     *
     * @param \Leevel\Session\ISession $session
     */
    public function setSession(ISession $session): void
    {
        $this->session = $session;
    }

    /**
     * 返回一个跳转响应.
     *
     * @param string $url
     * @param int    $status
     * @param array  $headers
     *
     * @return \Leevel\Http\RedirectResponse
     */
    protected function createRedirectResponse(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        $redirect = new RedirectResponse($url, $status, $headers);

        if (isset($this->session)) {
            $redirect->setSession($this->session);
        }

        $redirect->setRequest($this->url->getRequest());

        return $redirect;
    }
}
