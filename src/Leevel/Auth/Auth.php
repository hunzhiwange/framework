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
 * 认证驱动抽象类.
 */
abstract class Auth implements IAuth
{
    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
        'token' => null,
    ];

    /**
     * 构造函数.
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
    }

    /**
     * 用户是否已经登录.
     */
    public function isLogin(): bool
    {
        return $this->getLogin() ? true : false;
    }

    /**
     * 获取登录信息.
     */
    public function getLogin(): array
    {
        return $this->tokenData();
    }

    /**
     * 登录写入数据.
     */
    public function login(array $data, ?int $loginTime = null): void
    {
        $this->tokenPersistence($data, $loginTime);
    }

    /**
     * 登出.
     */
    public function logout(): void
    {
        $this->tokenDelete();
    }

    /**
     * 设置认证名字.
     */
    public function setTokenName(string $tokenName): void
    {
        $this->option['token'] = $tokenName;
    }

    /**
     * 取得认证名字.
     *
     * @throws \Leevel\Auth\AuthException
     */
    public function getTokenName(): string
    {
        if (!$this->option['token']) {
            throw new AuthException('Token name was not set.');
        }

        return $this->option['token'];
    }

    /**
     * 认证信息持久化.
     */
    protected function tokenPersistence(array $data, ?int $loginTime = null): void
    {
        $this->setPersistence($this->getTokenName(), json_encode($data), $loginTime);
    }

    /**
     * 认证信息获取.
     */
    protected function tokenData(): array
    {
        $data = $this->getPersistence($this->getTokenName());

        return $data ? json_decode($data, true, 512, JSON_THROW_ON_ERROR) : [];
    }

    /**
     * 认证信息删除.
     */
    protected function tokenDelete(): void
    {
        $this->deletePersistence($this->getTokenName());
    }

    /**
     * 数据持久化.
     */
    abstract protected function setPersistence(string $key, string $value, int $expire = 0): void;

    /**
     * 获取持久化数据.
     *
     * @return mixed
     */
    abstract protected function getPersistence(string $key);

    /**
     * 删除持久化数据.
     */
    abstract protected function deletePersistence(string $key): void;
}
