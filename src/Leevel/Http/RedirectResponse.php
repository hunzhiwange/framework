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

use InvalidArgumentException;
use Leevel\Session\ISession;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

/**
 * Redirect 响应请求.
 */
class RedirectResponse extends SymfonyRedirectResponse
{
    use BaseResponse;

    /**
     * HTTP 请求.
     *
     * @var \Leevel\Http\Request
     */
    protected ?Request $request = null;

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
     * @param null|mixed   $value
     */
    public function with($key, $value = null): void
    {
        $key = is_array($key) ? $key : [$key => $value];
        foreach ($key as $k => $v) {
            $this->session->flash($k, $v);
        }
    }

    /**
     * 闪存输入信息.
     */
    public function withInput(array $input = []): void
    {
        $input = $input ?: ($this->request ? $this->request->input() : []);
        $inputs = array_merge($this->session->getFlash('inputs', []), $input);
        $this->session->flash('inputs', $inputs);
    }

    /**
     * 闪存给定的 keys 输入信息.
     *
     * @throws \InvalidArgumentException
     */
    public function onlyInput(...$args): void
    {
        if (!$args) {
            $e = 'Method onlyInput need at least one arg.';

            throw new InvalidArgumentException($e);
        }

        $this->withInput($this->request ? $this->request->only($args) : []);
    }

    /**
     * 闪存排除给定的 keys 输入信息.
     *
     * @throws \InvalidArgumentException
     */
    public function exceptInput(...$args): void
    {
        if (!$args) {
            $e = 'Method exceptInput need at least one arg.';

            throw new InvalidArgumentException($e);
        }

        $this->withInput($this->request ? $this->request->except($args) : []);
    }

    /**
     * 闪存错误信息.
     *
     * @param mixed $value
     */
    public function withErrors($value, string $key = 'default'): void
    {
        $errors = $this->session->getFlash('errors', []);
        $errors[$key] = $value;
        $this->session->flash('errors', $errors);
    }

    /**
     * 获取 HTTP 请求.
     *
     * @return null|\Leevel\Http\Request
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }

    /**
     * 设置 HTTP 请求.
     *
     * @param \Leevel\Http\Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
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
