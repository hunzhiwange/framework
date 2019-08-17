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
 * auth 驱动抽象类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.07
 *
 * @version 1.0
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
     *
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
    }

    /**
     * 用户是否已经登录.
     *
     * @return bool
     */
    public function isLogin(): bool
    {
        return $this->getLogin() ? true : false;
    }

    /**
     * 获取登录信息.
     *
     * @return array
     */
    public function getLogin(): array
    {
        return $this->tokenData();
    }

    /**
     * 登录写入数据.
     *
     * @param array $data
     * @param int   $loginTime
     */
    public function login(array $data, int $loginTime = 0): void
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
     *
     * @param string $tokenName
     */
    public function setTokenName(string $tokenName): void
    {
        $this->option['token'] = $tokenName;
    }

    /**
     * 取得认证名字.
     *
     * @throws \Leevel\Auth\AuthException
     *
     * @return string
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
     *
     * @param array $data
     * @param int   $loginTime
     */
    protected function tokenPersistence(array $data, int $loginTime = 0): void
    {
        $this->setPersistence($this->getTokenName(), json_encode($data), $loginTime);
    }

    /**
     * 认证信息获取.
     *
     * @return array
     */
    protected function tokenData(): array
    {
        $data = $this->getPersistence($this->getTokenName());

        return $data ? json_decode($data, true) : [];
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
     *
     * @param string $key
     * @param string $value
     * @param int    $expire
     */
    abstract protected function setPersistence(string $key, string $value, int $expire = 0): void;

    /**
     * 获取持久化数据.
     *
     * @param string $key
     *
     * @return mixed
     */
    abstract protected function getPersistence(string $key);

    /**
     * 删除持久化数据.
     *
     * @param string $key
     */
    abstract protected function deletePersistence(string $key): void;
}
