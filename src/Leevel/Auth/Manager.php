<?php

declare(strict_types=1);

namespace Leevel\Auth;

use Leevel\Support\Manager as Managers;

/**
 * 认证管理器.
 *
 * @method static bool                  isLogin()                                      用户是否已经登录.
 * @method static array                 getLogin()                                     获取登录信息.
 * @method static string                login(array $data, ?int $loginTime = null)     登录写入数据.
 * @method static void                  logout()                                       登出.
 * @method static void                  setTokenName(string $tokenName)                设置认证名字.
 * @method static string                getTokenName()                                 取得认证名字.
 * @method static \Leevel\Di\IContainer container()                                    返回 IOC 容器.
 * @method static void                  disconnect(?string $connect = null)            删除连接.
 * @method static array                 getConnects()                                  取回所有连接.
 * @method static mixed                 getContainerOption(?string $name = null)       获取容器配置值.
 * @method static void                  setContainerOption(string $name, mixed $value) 设置容器配置值.
 * @method static void                  extend(string $connect, \Closure $callback)    扩展自定义连接.
 * @method static array                 normalizeConnectOption(string $connect)        整理连接配置.
 */
class Manager extends Managers
{
    /**
     * {@inheritDoc}
     */
    public function connect(?string $connect = null, bool $newConnect = false): IAuth
    {
        return parent::connect($connect, $newConnect);
    }

    /**
     * {@inheritDoc}
     */
    public function reconnect(?string $connect = null): IAuth
    {
        return parent::reconnect($connect);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultConnect(): string
    {
        $option = $this->getContainerOption('default');

        return (string) $this->getContainerOption($option.'_default');
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultConnect(string $name): void
    {
        $option = $this->getContainerOption('default');
        $this->setContainerOption($option.'_default', $name);
    }

    /**
     * 取得配置命名空间.
     */
    protected function getOptionNamespace(): string
    {
        return 'auth';
    }

    /**
     * 创建 session 连接.
     */
    protected function makeConnectSession(string $connect, ?string $driverClass = null): Session
    {
        $driverClass = $this->getDriverClass(Session::class, $driverClass);
        $options = $this->normalizeConnectOption($connect);

        return new $driverClass($this->container['session'], $options);
    }

    /**
     * 创建 token 连接.
     */
    protected function makeConnectToken(string $connect, ?string $driverClass = null): Token
    {
        $driverClass = $this->getDriverClass(Token::class, $driverClass);
        $options = $this->normalizeConnectOption($connect);

        return new $driverClass($this->container['cache'], $options);
    }

    /**
     * 创建 JWT 连接.
     */
    protected function makeConnectJwt(string $connect, ?string $driverClass = null): Jwt
    {
        $driverClass = $this->getDriverClass(Jwt::class, $driverClass);
        $options = $this->normalizeConnectOption($connect);

        return new $driverClass($options);
    }
}
