<?php

declare(strict_types=1);

namespace Leevel\Auth;

use Leevel\Manager\Manager as Managers;

/**
 * 认证管理器.
 *
 * @method static bool isLogin()                                  用户是否已经登录.
 * @method static array getLogin()                                获取登录信息.
 * @method static void login(array $data, ?int $loginTime = null) 登录写入数据.
 * @method static void logout()                                   登出.
 * @method static void setTokenName(string $tokenName)            设置认证名字.
 * @method static string getTokenName()                           取得认证名字.
 */
class Manager extends Managers
{
    /**
     * {@inheritDoc}
     */
    public function getDefaultConnect(): string
    {
        $option = $this->getContainerOption('default');

        return $this->getContainerOption($option.'_default');
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
    protected function makeConnectSession(string $connect): Session
    {
        $options = $this->normalizeConnectOption($connect);

        return new Session($this->container['session'], $options);
    }

    /**
     * 创建 token 连接.
     */
    protected function makeConnectToken(string $connect): Token
    {
        $options = $this->normalizeConnectOption($connect);

        return new Token($this->container['cache'], $this->container['request'], $options);
    }
}
