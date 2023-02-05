<?php

declare(strict_types=1);

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
 *     zh-CN:title="身份验证",
 *     path="component/auth",
 *     zh-CN:description="
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
 *
 * @internal
 *
 * @coversNothing
 */
final class ManagerTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="认证基本使用",
     *     zh-CN:description="
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
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $manager = $this->createManager();

        static::assertFalse($manager->isLogin());
        static::assertSame([], $manager->getLogin());

        static::assertNull($manager->login(['foo' => 'bar', 'hello' => 'world'], 10));

        static::assertTrue($manager->isLogin());
        static::assertSame(['foo' => 'bar', 'hello' => 'world'], $manager->getLogin());

        static::assertNull($manager->logout());

        static::assertFalse($manager->isLogin());
        static::assertSame([], $manager->getLogin());
    }

    /**
     * @api(
     *     zh-CN:title="setTokenName 设置认证名字",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithToken(): void
    {
        $manager = $this->createManagerWithToken();

        $manager->setTokenName('token');

        static::assertFalse($manager->isLogin());
        static::assertSame([], $manager->getLogin());

        static::assertNull($manager->login(['foo' => 'bar', 'hello' => 'world'], 10));

        static::assertTrue($manager->isLogin());
        static::assertSame(['foo' => 'bar', 'hello' => 'world'], $manager->getLogin());

        static::assertNull($manager->logout());

        static::assertFalse($manager->isLogin());
        static::assertSame([], $manager->getLogin());
    }

    /**
     * @api(
     *     zh-CN:title="setDefaultConnect 设置默认驱动",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSetDefaultDriver(): void
    {
        $manager = $this->createManagerWithTokenAndSession();

        $manager->setDefaultConnect('token');

        $manager->setTokenName('token');

        static::assertFalse($manager->isLogin());
        static::assertSame([], $manager->getLogin());

        static::assertNull($manager->login(['foo' => 'bar', 'hello' => 'world'], 10));

        static::assertTrue($manager->isLogin());
        static::assertSame(['foo' => 'bar', 'hello' => 'world'], $manager->getLogin());

        static::assertNull($manager->logout());

        static::assertFalse($manager->isLogin());
        static::assertSame([], $manager->getLogin());
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
        static::assertSame('token', $request->get('input_token'));

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
                'default' => 'web',
                'web_default' => 'session',
                'api_default' => 'token',
                'connect' => [
                    'session' => [
                        'driver' => 'session',
                        'token' => 'token',
                    ],
                    'token' => [
                        'driver' => 'token',
                        'token' => null,
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
                'default' => 'api',
                'web_default' => 'session',
                'api_default' => 'token',
                'connect' => [
                    'session' => [
                        'driver' => 'session',
                        'token' => 'token',
                    ],
                    'token' => [
                        'driver' => 'token',
                        'token' => null,
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
                'default' => 'web',
                'web_default' => 'session',
                'api_default' => 'token',
                'connect' => [
                    'session' => [
                        'driver' => 'session',
                        'token' => 'token',
                    ],
                    'token' => [
                        'driver' => 'token',
                        'token' => null,
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
