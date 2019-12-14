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

namespace Leevel\Auth;

/**
 * IAuth 接口.
 */
interface IAuth
{
    /**
     * 用户是否已经登录.
     */
    public function isLogin(): bool;

    /**
     * 获取登录信息.
     */
    public function getLogin(): array;

    /**
     * 登录写入数据.
     */
    public function login(array $data, int $loginTime = 0): void;

    /**
     * 登出.
     */
    public function logout(): void;

    /**
     * 设置认证名字.
     */
    public function setTokenName(string $tokenName): void;

    /**
     * 取得认证名字.
     *
     * @throws \Leevel\Auth\AuthException
     */
    public function getTokenName(): string;
}
