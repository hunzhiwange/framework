<?php

declare(strict_types=1);

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
     * 实现魔术方法 __callStatic.
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
