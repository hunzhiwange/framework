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

namespace Leevel\Auth;

/**
 * 代理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.09
 *
 * @version 1.0
 */
trait Proxy
{
    /**
     * 用户是否已经登录.
     *
     * @return bool
     */
    public function isLogin(): bool
    {
        return $this->proxy()->isLogin();
    }

    /**
     * 获取登录信息.
     *
     * @return array
     */
    public function getLogin(): array
    {
        return $this->proxy()->getLogin();
    }

    /**
     * 登录写入数据.
     *
     * @param array $data
     * @param int   $loginTime
     */
    public function login(array $data, int $loginTime = 0): void
    {
        $this->proxy()->login($data, $loginTime);
    }

    /**
     * 登出.
     */
    public function logout(): void
    {
        $this->proxy()->logout();
    }

    /**
     * 设置认证名字.
     *
     * @param string $tokenName
     */
    public function setTokenName(string $tokenName): void
    {
        $this->proxy()->setTokenName($tokenName);
    }

    /**
     * 取得认证名字.
     *
     * @return string
     */
    public function getTokenName(): string
    {
        return $this->proxy()->getTokenName();
    }

    /**
     * 返回代理.
     *
     * @return \Leevel\Auth\IAuth
     */
    public function proxy(): IAuth
    {
        return $this->connect();
    }
}
