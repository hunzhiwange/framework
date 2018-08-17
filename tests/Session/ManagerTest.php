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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Session;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Option\Option;
use Leevel\Session\Manager;
use SessionHandlerInterface;
use Tests\TestCase;

/**
 * manager test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.17
 *
 * @version 1.0
 */
class ManagerTest extends TestCase
{
    public function testBaseUse()
    {
        $manager = $this->createManager();

        $this->assertFalse($manager->isStart());
        $this->assertNull($manager->getId());
        $this->assertNull($manager->getName());

        $manager->start();
        $this->assertTrue($manager->isStart());

        $manager->set('hello', 'world');
        $this->assertSame(['hello' => 'world'], $manager->all());
        $this->assertTrue($manager->has('hello'));
        $this->assertSame('world', $manager->get('hello'));

        $manager->delete('hello');
        $this->assertSame([], $manager->all());
        $this->assertFalse($manager->has('hello'));
        $this->assertNull($manager->get('hello'));

        $manager->start();
        $this->assertTrue($manager->isStart());

        $this->assertInstanceof(SessionHandlerInterface::class, $manager->getConnect());
    }

    protected function createManager()
    {
        $container = new Container();

        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'session' => [
                'default' => 'nulls',

                'id' => null,

                'name' => 'queryphp',

                'cookie_domain' => null,

                'prefix' => 'sess_',

                'expire' => 86400,

                'connect' => [
                    'nulls' => [
                        'driver' => 'nulls',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        return $manager;
    }
}
