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

namespace Tests\Kernel;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Kernel\App;
use Leevel\Kernel\Helper;
use Tests\TestCase;

/**
 * helper test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.10
 *
 * @version 1.0
 */
class HelperTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testApp(): void
    {
        $container = $this->createContainer();
        $app = f('Leevel\\Kernel\\Helper\\app');
        $container->alias([
            'app' => [
                'Leevel\\Leevel\\App',
                'Leevel\\Kernel\\IApp',
            ],
        ]);

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(App::class, $app);
        $this->assertInstanceof(App::class, $container->make('app'));
        $this->assertSame('fooNotFound', $container->make('fooNotFound'));
    }

    public function testAppHelper(): void
    {
        $container = $this->createContainer();
        $app = Helper::app();
        $container->alias([
            'app' => [
                'Leevel\\Leevel\\App',
                'Leevel\\Kernel\\IApp',
            ],
        ]);

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(App::class, $app);
        $this->assertInstanceof(App::class, $container->make('app'));
        $this->assertSame('fooNotFound', $container->make('fooNotFound'));
    }

    public function testHelperNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Call to undefined function Leevel\\Kernel\\Helper\\not_found()'
        );

        $this->assertFalse(Helper::notFound());
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();
        $container->instance('app', new App($container, ''));

        return $container;
    }
}
