<?php

declare(strict_types=1);

namespace Leevel\Auth;

use Leevel\Support\Manager as Managers;

/**
 * 认证管理器.
 */
class Manager extends Managers implements IAuth
{
    public function isLogin(): bool
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function getLogin(): array
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function login(array $data, ?int $loginTime = null): string
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $data, $loginTime);
    }

    public function logout(): void
    {
        $this->callConnectMethod(__FUNCTION__);
    }

    public function setTokenName(string $tokenName): void
    {
        $this->callConnectMethod(__FUNCTION__, $tokenName);
    }

    public function getTokenName(): string
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function connect(?string $connect = null, bool $newConnect = false, mixed ...$arguments): IAuth
    {
        return parent::connect($connect, $newConnect, ...$arguments);
    }

    public function reconnect(?string $connect = null, mixed ...$arguments): IAuth
    {
        return parent::reconnect($connect, ...$arguments);
    }

    public function getDefaultConnect(): string
    {
        $config = $this->getContainerConfig('default');

        return (string) $this->getContainerConfig($config.'_default');
    }

    public function setDefaultConnect(string $name): void
    {
        $config = $this->getContainerConfig('default');
        $this->setContainerConfig($config.'_default', $name);
    }

    /**
     * 取得配置命名空间.
     */
    protected function getConfigNamespace(): string
    {
        return 'auth';
    }

    /**
     * 创建 session 连接.
     */
    protected function makeConnectSession(string $connect, ?string $driverClass = null): Session
    {
        $driverClass = $this->getDriverClass(Session::class, $driverClass);
        $configs = $this->normalizeConnectConfig($connect);

        return new $driverClass($this->container['session'], $configs);
    }

    /**
     * 创建 token 连接.
     */
    protected function makeConnectToken(string $connect, ?string $driverClass = null): Token
    {
        $driverClass = $this->getDriverClass(Token::class, $driverClass);
        $configs = $this->normalizeConnectConfig($connect);

        return new $driverClass($this->container['cache'], $configs);
    }

    /**
     * 创建 JWT 连接.
     */
    protected function makeConnectJwt(string $connect, ?string $driverClass = null): Jwt
    {
        $driverClass = $this->getDriverClass(Jwt::class, $driverClass);
        $configs = $this->normalizeConnectConfig($connect);

        return new $driverClass($configs);
    }
}
