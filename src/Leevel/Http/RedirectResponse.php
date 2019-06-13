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

namespace Leevel\Http;

use InvalidArgumentException;
use Leevel\Session\ISession;

/**
 * Redirect 响应请求
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.28
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class RedirectResponse extends Response
{
    /**
     * 目标 URL 地址
     *
     * @var string
     */
    protected $targetUrl;

    /**
     * HTTP 请求
     *
     * @var \Leevel\Http\IRequest
     */
    protected $request;

    /**
     * SESSION 仓储.
     *
     * @var \Leevel\Session\ISession
     */
    protected $session;

    /**
     * 构造函数.
     *
     * @param string $url
     * @param int    $status
     * @param array  $headers
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(?string $url = null, int $status = 302, array $headers = [])
    {
        parent::__construct('', $status, $headers);

        if ($url) {
            $this->setTargetUrl($url);
        }

        if (!$this->isRedirect()) {
            $e = sprintf('The HTTP status code is not a redirect (%s given).', $status);

            throw new InvalidArgumentException($e);
        }

        if (301 === $status && !array_key_exists('cache-control', $headers)) {
            $this->headers->remove('cache-control');
        }
    }

    /**
     * 创建 URL 跳转响应.
     *
     * @param mixed $url
     * @param int   $status
     * @param array $headers
     *
     * @return static
     */
    public static function create($url = '', int $status = 302, array $headers = []): IResponse
    {
        return new static($url, $status, $headers);
    }

    /**
     * 闪存一个数据片段到 SESSION.
     *
     * @param array|string $key
     * @param mixed        $value
     *
     * @return \Leevel\Http\IResponse
     */
    public function with($key, $value = null): IResponse
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $key = is_array($key) ? $key : [
            $key => $value,
        ];

        foreach ($key as $k => $v) {
            $this->session->flash($k, $v);
        }

        return $this;
    }

    /**
     * 闪存输入信息.
     *
     * @param null|array $input
     *
     * @return \Leevel\Http\IResponse
     */
    public function withInput(?array $input = null): IResponse
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $input = $input ?: $this->request->input();

        $inputs = array_merge($this->session->getFlash('inputs', []), $input);

        $this->session->flash('inputs', $inputs);

        return $this;
    }

    /**
     * 闪存输入信息.
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Http\IResponse
     */
    public function onlyInput(...$args): IResponse
    {
        if (!$args) {
            $e = 'Method onlyInput need an args.';

            throw new InvalidArgumentException($e);
        }

        return $this->withInput($this->request->only($args));
    }

    /**
     * 闪存输入信息.
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Http\IResponse
     */
    public function exceptInput(...$args): IResponse
    {
        if (!$args) {
            $e = 'Method exceptInput need an args.';

            throw new InvalidArgumentException($e);
        }

        return $this->withInput($this->request->except($args));
    }

    /**
     * 闪存错误信息.
     *
     * @param mixed  $value
     * @param string $key
     *
     * @return \Leevel\Http\IResponse
     */
    public function withErrors($value, string $key = 'default'): IResponse
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $errors = $this->session->getFlash('errors', []);
        $errors[$key] = $value;

        $this->session->flash('errors', $errors);

        return $this;
    }

    /**
     * 获取目标 URL 地址.
     *
     * @return string
     */
    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }

    /**
     * 设置目标 URL 地址
     *
     * @param string $url
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Http\IResponse
     */
    public function setTargetUrl(string $url): IResponse
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (empty($url)) {
            $e = 'Cannot redirect to an empty URL.';

            throw new InvalidArgumentException($e);
        }

        $this->targetUrl = $url;

        $this->setContent(
            sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="refresh" content="0;url=%1$s" />
        <title>Redirecting to %1$s</title>
    </head>
    <body>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8')));

        $this->headers->set('Location', $url);

        return $this;
    }

    /**
     * 获取 HTTP 请求
     *
     * @return null|\Leevel\Http\IRequest
     */
    public function getRequest(): ?IRequest
    {
        return $this->request;
    }

    /**
     * 设置 HTTP 请求
     *
     * @param \Leevel\Http\IRequest $request
     */
    public function setRequest(IRequest $request): void
    {
        $this->request = $request;
    }

    /**
     * 获取 SESSION 仓储.
     *
     * @return null|\Leevel\Session\ISession
     */
    public function getSession(): ?ISession
    {
        return $this->session;
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
}
