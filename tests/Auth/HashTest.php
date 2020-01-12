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

namespace Tests\Auth;

use Leevel\Auth\Hash;
use Tests\TestCase;

/**
 * @api(
 *     title="Auth hash",
 *     path="component/auth/hash",
 *     description="
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
 */
class HashTest extends TestCase
{
    protected function setUp(): void
    {
        if (isset($_SERVER['SUDO_USER']) &&
            'vagrant' === $_SERVER['SUDO_USER']) {
            $this->markTestSkipped('Ignore hash error.');
        }
    }

    /**
     * @api(
     *     title="密码哈希基本使用",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $hash = new Hash();
        $hashPassword = $hash->password('123456');
        $this->assertTrue($hash->verify('123456', $hashPassword));
    }

    /**
     * @api(
     *     title="密码哈希带配置例子",
     *     description="
     * 底层使用的是 `password_hash` 函数，详细见下面的链接。
     *
     * <https://www.php.net/manual/zh/function.password-hash.php>
     * ",
     *     note="",
     * )
     */
    public function testWithCost(): void
    {
        $hash = new Hash();
        $hashPassword = $hash->password('123456', ['cost' => 12]);
        $this->assertTrue($hash->verify('123456', $hashPassword));
    }
}
