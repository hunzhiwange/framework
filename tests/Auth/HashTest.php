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
 * QueryPHP 提供了一组简单的认证组件用于登陆验证，通常我们使用代理 `\Leevel\Auth\Proxy\Auth` 类进行静态调用。
 *
 * 内置支持的认证驱动类型包括 session、token，分别用于 web 和 api 的认证服务。
 *
 * ## 使用方式
 *
 * 使用容器 auths 服务
 *
 * ``` php
 * \App::make('auths')->login(array $data, int $loginTime = 0): void;
 * ```
 *
 * 依赖注入
 *
 * ``` php
 * class Demo
 * {
 *     private $auth;
 *
 *     public function __construct(\Leevel\Auth\Manager $auth)
 *     {
 *         $this->auth = $auth;
 *     }
 * }
 * ```
 *
 * 使用静态代理
 *
 * ``` php
 * \Leevel\Auth\Proxy\Auth::login(array $data, int $loginTime = 0): void;
 * ```
 *
 * ## auth 配置
 *
 * 系统的 auth 配置位于应用下面的 `option/auth.php` 文件。
 *
 * 可以定义多个认证连接，并且支持切换，每一个连接支持驱动设置。
 *
 * ``` php
 * {[file_get_contents('option/auth.php')]}
 * ```
 *
 * auth 参数根据不同的连接会有所区别，通用的 auth 参数如下：
 *
 * |配置项|配置描述|
 * |:-|:-|
 * |web_default|WEB 认证驱动连接|
 * |api_default|API 认证驱动连接|
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
