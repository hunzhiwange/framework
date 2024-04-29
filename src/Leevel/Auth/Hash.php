<?php

declare(strict_types=1);

namespace Leevel\Auth;

/**
 * Hash.
 */
class Hash implements IHash
{
    /**
     * 加密算法.
     */
    public const string ALGO = ':algo';

    public function password(string $password, array $config = []): string
    {
        $algo = $config[self::ALGO] ?? PASSWORD_BCRYPT;

        return password_hash($password, (int) $algo, $config);
    }

    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
