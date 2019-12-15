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

namespace Tests\Session\Provider;

use Leevel\Di\Container;
use Leevel\Option\Option;
use Leevel\Session\Provider\Register;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // sessions
        $manager = $container->make('sessions');

        $this->assertFalse($manager->isStart());
        $this->assertNull($manager->getId());
        $this->assertSame('UID', $manager->getName());

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

        // session
        $test = $container->make('session');
        $this->assertTrue($test->isStart());

        $test->set('hello', 'world');
        $this->assertSame(['hello' => 'world'], $test->all());
        $this->assertTrue($test->has('hello'));
        $this->assertSame('world', $test->get('hello'));

        $test->delete('hello');
        $this->assertSame([], $test->all());
        $this->assertFalse($test->has('hello'));
        $this->assertNull($test->get('hello'));

        $test->start();
        $this->assertTrue($test->isStart());
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $option = new Option([
            'session' => [
                'default'       => 'test',
                'id'            => null,
                'name'          => 'UID',
                'expire'        => 86400,
                'connect'       => [
                    'test' => [
                        'driver' => 'test',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        return $container;
    }
}
