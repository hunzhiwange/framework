<?php

declare(strict_types=1);

namespace Leevel\Auth\Proxy;

use Leevel\Auth\Manager;
use Leevel\Di\Container;

/**
 * 代理 auth.
 *
 * @method static bool                  isLogin()                                                  用户是否已经登录.
 * @method static array                 getLogin()                                                 获取登录信息.
 * @method static void                  login(array $data, ?int $loginTime = null)                 登录写入数据.
 * @method static void                  logout()                                                   登出.
 * @method static void                  setTokenName(string $tokenName)                            设置认证名字.
 * @method static string                getTokenName()                                             取得认证名字.
 * @method static \Leevel\Di\IContainer container()                                                返回 IOC 容器.
 * @method static \Leevel\Auth\IAuth    connect(?string $connect = null, bool $newConnect = false) 连接并返回连接对象.
 * @method static \Leevel\Auth\IAuth    reconnect(?string $connect = null)                         重新连接.
 * @method static void                  disconnect(?string $connect = null)                        删除连接.
 * @method static array                 getConnects()                                              取回所有连接.
 * @method static string                getDefaultConnect()                                        返回默认连接.
 * @method static void                  setDefaultConnect(string $name)                            设置默认连接.
 * @method static mixed                 getContainerOption(?string $name = null)                   获取容器配置值.
 * @method static void                  setContainerOption(string $name, mixed $value)             设置容器配置值.
 * @method static void                  extend(string $connect, \Closure $callback)                扩展自定义连接.
 * @method static array                 normalizeConnectOption(string $connect)                    整理连接配置.
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
