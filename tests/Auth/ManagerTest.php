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

use Leevel\Auth\Manager;
use Leevel\Cache\File as CacheFile;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\Request;
use Leevel\Option\Option;
use Leevel\Session\File as SessionFile;
use Tests\TestCase;

/**
 * @api(
 *     title="Auth",
 *     path="component/auth",
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
 * \App::make('auths')->login(array $data, ?int $loginTime = null): void;
 * ```
 *
 * 依赖注入
 *
 * ``` php
 * class Demo
 * {
 *     private \Leevel\Auth\Manager $auth;
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
 * \Leevel\Auth\Proxy\Auth::login(array $data, ?int $loginTime = null): void;
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
class ManagerTest extends TestCase
{
    /**
     * @api(
     *     title="认证基本使用",
     *     description="
     * **login 原型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Auth\IAuth::class, 'login', 'define')]}
     * ```
     *
     * `$loginTime` 过期时间规则如下：
     *
     *   * null 表示默认登陆缓存时间
     *   * 小与等于 0 表示永久缓存
     *   * 其它表示缓存多少时间，单位
     * ",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $manager = $this->createManager();

        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());

        $this->assertNull($manager->login(['foo' => 'bar', 'hello' => 'world'], 10));

        $this->assertTrue($manager->isLogin());
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $manager->getLogin());

        $this->assertNull($manager->logout());

        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());
    }

    /**
     * @api(
     *     title="setTokenName 设置认证名字",
     *     description="",
     *     note="",
     * )
     */
    public function testWithToken(): void
    {
        $manager = $this->createManagerWithToken();

        $manager->setTokenName('token');

        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());

        $this->assertNull($manager->login(['foo' => 'bar', 'hello' => 'world'], 10));

        $this->assertTrue($manager->isLogin());
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $manager->getLogin());

        $this->assertNull($manager->logout());

        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());
    }

    /**
     * @api(
     *     title="setDefaultConnect 设置默认驱动",
     *     description="",
     *     note="",
     * )
     */
    public function testSetDefaultDriver(): void
    {
        $manager = $this->createManagerWithTokenAndSession();

        $manager->setDefaultConnect('token');

        $manager->setTokenName('token');

        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());

        $this->assertNull($manager->login(['foo' => 'bar', 'hello' => 'world'], 10));

        $this->assertTrue($manager->isLogin());
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $manager->getLogin());

        $this->assertNull($manager->logout());

        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());
    }

    protected function createCache(): CacheFile
    {
        return new CacheFile([
            'path' => __DIR__.'/cacheFile',
        ]);
    }

    protected function createSession(): SessionFile
    {
        $session = new SessionFile(new CacheFile([
            'path' => __DIR__.'/cache',
        ]));

        $session->start();

        return $session;
    }

    protected function createRequest(): Request
    {
        $request = $this->createMock(Request::class);

        $request->method('get')->willReturn('token');
        $this->assertSame('token', $request->get('input_token'));

        return $request;
    }

    protected function createManager(): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'auth' => [
                'default'     => 'web',
                'web_default' => 'session',
                'api_default' => 'token',
                'connect'     => [
                    'session' => [
                        'driver' => 'session',
                        'token'  => 'token',
                    ],
                    'token' => [
                        'driver'      => 'token',
                        'token'       => null,
                        'input_token' => 'token',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);
        $container->singleton('session', $this->createSession());

        return $manager;
    }

    protected function createManagerWithToken(): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'auth' => [
                'default'     => 'api',
                'web_default' => 'session',
                'api_default' => 'token',
                'connect'     => [
                    'session' => [
                        'driver' => 'session',
                        'token'  => 'token',
                    ],
                    'token' => [
                        'driver'      => 'token',
                        'token'       => null,
                        'input_token' => 'token',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);
        $container->singleton('cache', $this->createCache());
        $container->singleton('request', $this->createRequest());

        return $manager;
    }

    protected function createManagerWithTokenAndSession(): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'auth' => [
                'default'     => 'web',
                'web_default' => 'session',
                'api_default' => 'token',
                'connect'     => [
                    'session' => [
                        'driver' => 'session',
                        'token'  => 'token',
                    ],
                    'token' => [
                        'driver'      => 'token',
                        'token'       => null,
                        'input_token' => 'token',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);
        $container->singleton('session', $this->createSession());
        $container->singleton('cache', $this->createCache());
        $container->singleton('request', $this->createRequest());

        return $manager;
    }
}
