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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
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
     */
    public function __construct(?string $url, int $status = 302, array $headers = [])
    {
        parent::__construct('', $status, $headers);

        $this->setTargetUrl($url);

        if (! $this->isRedirect()) {
            throw new InvalidArgumentException(sprintf('The HTTP status code is not a redirect ("%s" given).', $status));
        }

        if (301 == $status && ! array_key_exists('cache-control', $headers)) {
            $this->headers->remove('cache-control');
        }
    }

    /**
     * 创建 URL 跳转响应.
     *
     * @param string $url
     * @param int    $status
     * @param array  $headers
     *
     * @return static
     */
    public static function create($url = '', int $status = 302, array $headers = [])
    {
        return new static($url, $status, $headers);
    }

    /**
     * 闪存一个数据片段到 SESSION.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return $this
     */
    public function with($key, $value = null)
    {
        if ($this->checkTControl()) {
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
     * @param array $input
     *
     * @return $this
     */
    public function withInput(array $input = null)
    {
        if ($this->checkTControl()) {
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
     * @return $this
     */
    public function onlyInput()
    {
        $args = func_get_args();
        if (! $args) {
            throw new InvalidArgumentException('Method onlyInput need an args.');
        }

        return $this->withInput($this->request->only($args));
    }

    /**
     * 闪存输入信息.
     *
     * @return $this
     */
    public function exceptInput()
    {
        $args = func_get_args();
        if (! $args) {
            throw new InvalidArgumentException('Method exceptInput need an args.');
        }

        return $this->withInput($this->request->except($args));
    }

    /**
     * 闪存错误信息.
     *
     * @param mixed  $value
     * @param string $key
     *
     * @return $this
     */
    public function withErrors($value, string $key = 'default')
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $errors = $this->session->getFlash('errors', []);
        $errors[$key] = $value;

        $this->session->flash('errors', $errors);

        return $this;
    }

    /**
     * 获取目标 URL 地址
     *
     * @return string
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * 设置目标 URL 地址
     *
     * @param string $url
     *
     * @return $this
     */
    public function setTargetUrl($url)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (empty($url)) {
            throw new InvalidArgumentException('Cannot redirect to an empty URL.');
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
     * @return \Leevel\Http\IRequest|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * 设置 HTTP 请求
     *
     * @param \Leevel\Http\IRequest $request
     */
    public function setRequest(IRequest $request)
    {
        $this->request = $request;
    }

    /**
     * 获取 SESSION 仓储.
     *
     * @return \Leevel\Session\ISession|null
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * 设置 SESSION 仓储.
     *
     * @param \Leevel\Session\ISession $session
     */
    public function setSession(ISession $session)
    {
        $this->session = $session;
    }
}
