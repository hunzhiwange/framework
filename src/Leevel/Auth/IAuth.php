<?php

declare(strict_types=1);

namespace Leevel\Auth;

/**
 * 认证接口.
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
    public function login(array $data, ?int $loginTime = null): void;

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
