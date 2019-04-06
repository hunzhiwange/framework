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

namespace Tests\Leevel\Bootstrap;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Leevel\App as Apps;
use Leevel\Leevel\Bootstrap\TraverseProvider;
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
    public function testBaseUse()
    {
        $bootstrap = new TraverseProvider();

        $project = new App2($appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $project);
        $this->assertInstanceof(Container::class, $project);

        $option = new OptionTest();

        $project->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertNull($bootstrap->handle($project));

        // for deferredAlias
        $this->assertArrayNotHasKey('providerDeferTest1', $_SERVER);
        $this->assertSame('bar', $project->make('foo'));
        $this->assertSame('bar', $project->make(ProviderDeferTest1::class));
        $this->assertSame(1, $_SERVER['providerDeferTest1']);

        // for providers
        $this->assertSame(1, $_SERVER['testRegisterProvidersRegister']);
        $this->assertSame(1, $_SERVER['testRegisterProvidersBootstrap']);
        $this->assertTrue($project->isBootstrap());

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
                    'foo' => 'Tests\\Leevel\\Bootstrap\\ProviderDeferTest1',
                ],
                [
                    'Tests\\Leevel\\Bootstrap\\ProviderDeferTest1' => [
                        'foo' => [
                            'Tests\\Leevel\\Bootstrap\\ProviderDeferTest1',
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
                'Tests\\Leevel\\Bootstrap\\ProviderDeferTest1',
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
    public function __construct(App2 $project)
    {
    }

    public function bootstrap()
    {
        $_SERVER['testRegisterProvidersBootstrap'] = 1;
    }

    public function register(): void
    {
        $_SERVER['testRegisterProvidersRegister'] = 1;
    }
}
