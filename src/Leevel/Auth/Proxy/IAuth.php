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

namespace Leevel\Auth\Proxy;

/**
 * 代理 auth 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.06
 *
 * @version 1.0
 *
 * @see \Leevel\Auth\IAuth 请保持接口设计的一致性
 */
interface IAuth
{
    /**
     * 用户是否已经登录.
     *
     * @return bool
     */
    public static function isLogin(): bool;

    /**
     * 获取登录信息.
     *
     * @return array
     */
    public static function getLogin(): array;

    /**
     * 登录写入数据.
     *
     * @param array $data
     * @param int   $loginTime
     */
    public static function login(array $data, int $loginTime = 0): void;

    /**
     * 登出.
     */
    public static function logout(): void;

    /**
     * 设置认证名字.
     *
     * @param string $tokenName
     */
    public static function setTokenName(string $tokenName): void;

    /**
     * 取得认证名字.
     *
     * @return string
     */
    public static function getTokenName(): string;
}
