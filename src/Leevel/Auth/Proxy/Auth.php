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

namespace Leevel\Auth\Proxy;

use Leevel\Auth\Manager;
use Leevel\Di\Container;

/**
 * 代理 auth.
 *
 * @method static bool isLogin()                                  用户是否已经登录.
 * @method static array getLogin()                                获取登录信息.
 * @method static void login(array $data, ?int $loginTime = null) 登录写入数据.
 * @method static void logout()                                   登出.
 * @method static void setTokenName(string $tokenName)            设置认证名字.
 * @method static string getTokenName()                           取得认证名字.
 */
class Auth
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        return Container::singletons()->make('auths');
    }
}
