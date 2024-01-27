<?php

declare(strict_types=1);

namespace Leevel\Auth;

/**
 * IHash 接口.
 */
interface IHash
{
    /**
     * 生成密码.
     */
    public function password(string $password, array $config = []): string;

    /**
     * 校验密码.
     */
    public function verify(string $password, string $hash): bool;
}
