<?php

declare(strict_types=1);

namespace Leevel\Encryption;

/**
 * IEncryption 接口.
 */
interface IEncryption
{
    /**
     * 加密.
     */
    public function encrypt(string $value, int $expiry = 0): string;

    /**
     * 解密.
     */
    public function decrypt(string $value): string;
}
