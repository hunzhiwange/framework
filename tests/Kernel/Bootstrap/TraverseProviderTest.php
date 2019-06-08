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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Kernel\Bootstrap;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Bootstrap\TraverseProvider;
use Leevel\Kernel\IApp;
use Tests\TestCase;

/**
 * traverseProvider test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.20
 *
 * @version 1.0
 */
class TraverseProviderTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

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

        $this->assertNull($bootstrap->handle($app));

        // for deferredAlias
        $this->assertArrayNotHasKey('providerDeferTest1', $_SERVER);
        $container->alias(ProviderDeferTest1::providers());
        $this->assertSame('bar', $container->make('foo'));
        $this->assertSame('bar', $container->make(ProviderDeferTest1::class));
        $this->assertSame(1, $_SERVER['providerDeferTest1']);

        // for providers
        $this->assertSame(1, $_SERVER['testRegisterProvidersRegister']);
        $this->assertSame(1, $_SERVER['testRegisterProvidersBootstrap']);
        $this->assertTrue($container->isBootstrap());

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
        if ('_deferred_providers' === $name) {
            return [
                [
                    'foo' => 'Tests\\Kernel\\Bootstrap\\ProviderDeferTest1',
                ],
                [
                    'Tests\\Kernel\\Bootstrap\\ProviderDeferTest1' => [
                        'foo' => [
                            'Tests\\Kernel\\Bootstrap\\ProviderDeferTest1',
                        ],
                    ],
                ],
            ];
        }

        if ('_composer.providers' === $name) {
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
                'Tests\\Kernel\\Bootstrap\\ProviderDeferTest1',
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
