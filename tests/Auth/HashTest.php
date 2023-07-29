<?php

declare(strict_types=1);

namespace Tests\Auth;

use Leevel\Auth\Hash;
use Tests\TestCase;

/**
 * @api(
 *     title="Auth hash",
 *     zh-CN:title="身份验证哈希",
 *     path="component/auth/hash",
 *     zh-CN:description="
 * 密码哈希主要用于登陆验证密码，功能非常简单，仅提供密码加密方法 `password` 和校验方法 `verify`。
 *
 * **password 原型**
 *
 * ``` php
 * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Auth\Hash::class, 'password', 'define')]}
 * ```
 *
 * **verify 原型**
 *
 * ``` php
 * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Auth\Hash::class, 'verify', 'define')]}
 * ```
 * ",
 * )
 *
 * @internal
 */
final class HashTest extends TestCase
{
    protected function setUp(): void
    {
        if (isset($_SERVER['SUDO_USER']) && 'vagrant' === $_SERVER['SUDO_USER']) {
            static::markTestSkipped('Ignore hash error.');
        }
    }

    /**
     * @api(
     *     zh-CN:title="密码哈希基本使用",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $hash = new Hash();
        $hashPassword = $hash->password('123456');
        static::assertTrue($hash->verify('123456', $hashPassword));
    }

    /**
     * @api(
     *     zh-CN:title="密码哈希带配置例子",
     *     zh-CN:description="
     * 底层使用的是 `password_hash` 函数，详细见下面的链接。
     *
     * <https://www.php.net/manual/zh/function.password-hash.php>
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testWithCost(): void
    {
        $hash = new Hash();
        $hashPassword = $hash->password('123456', ['cost' => 12]);
        static::assertTrue($hash->verify('123456', $hashPassword));
    }
}
