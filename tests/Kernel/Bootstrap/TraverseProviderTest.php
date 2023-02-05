<?php

declare(strict_types=1);

namespace Tests\Kernel\Bootstrap;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Bootstrap\TraverseProvider;
use Leevel\Kernel\IApp;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="初始化遍历服务提供者注册服务",
 *     path="architecture/kernel/bootstrap/traverseprovider",
 *     zh-CN:description="
 * QueryPHP 在内核执行过程中会执行初始化，分为 4 个步骤，载入配置、载入语言包、注册异常运行时和遍历服务提供者注册服务。
 *
 * 内核初始化，包括 `\Leevel\Kernel\IKernel::bootstrap` 和 `\Leevel\Kernel\IKernelConsole::bootstrap` 均会执行上述 4 个步骤。
 * ",
 *     zh-CN:note="",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class TraverseProviderTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    /**
     * @api(
     *     zh-CN:title="基本使用方法",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Kernel\Bootstrap\OptionTest**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Bootstrap\OptionTest::class)]}
     * ```
     *
     * **Tests\Kernel\Bootstrap\ProviderDeferTest1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Bootstrap\ProviderDeferTest1::class)]}
     * ```
     *
     * **Tests\Kernel\Bootstrap\ProviderTest3**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Bootstrap\ProviderTest3::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $bootstrap = new TraverseProvider();

        $container = Container::singletons();
        $app = new App2($container, $appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        $option = new OptionTest();

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        static::assertNull($bootstrap->handle($app));

        // for deferredAlias
        static::assertArrayNotHasKey('providerDeferTest1', $_SERVER);
        $container->alias(ProviderDeferTest1::providers());
        static::assertSame('bar', $container->make('foo'));
        static::assertSame('bar', $container->make(ProviderDeferTest1::class));
        static::assertSame(1, $_SERVER['providerDeferTest1']);

        // for providers
        static::assertSame(1, $_SERVER['testRegisterProvidersRegister']);
        static::assertSame(1, $_SERVER['testRegisterProvidersBootstrap']);
        static::assertTrue($container->isBootstrap());

        unset(
            $_SERVER['providerDeferTest1'],
            $_SERVER['testRegisterProvidersRegister'],
            $_SERVER['testRegisterProvidersBootstrap']
        );
    }
}

class App2 extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class OptionTest
{
    public function get(string $name)
    {
        if (':deferred_providers' === $name) {
            return [
                [
                    'foo' => ProviderDeferTest1::class,
                ],
                [
                    ProviderDeferTest1::class => [
                        'foo' => [
                            ProviderDeferTest1::class,
                        ],
                    ],
                ],
            ];
        }

        if (':composer.providers' === $name) {
            return [ProviderTest3::class];
        }
    }
}

class ProviderDeferTest1 extends Provider
{
    public function register(): void
    {
        $_SERVER['providerDeferTest1'] = 1;

        $this->container->singleton('foo', function (IContainer $container) {
            return 'bar';
        });
    }

    public static function providers(): array
    {
        return [
            'foo' => [
                self::class,
            ],
        ];
    }

    public static function isDeferred(): bool
    {
        return true;
    }
}

class ProviderTest3 extends Provider
{
    public function __construct(IContainer $container)
    {
    }

    public function bootstrap(): void
    {
        $_SERVER['testRegisterProvidersBootstrap'] = 1;
    }

    public function register(): void
    {
        $_SERVER['testRegisterProvidersRegister'] = 1;
    }
}
