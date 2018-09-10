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

namespace Tests\Cache\Provider;

use Leevel\Cache\Provider\Register;
use Leevel\Di\Container;
use Leevel\Filesystem\Fso;
use Leevel\Option\Option;
use Tests\TestCase;

/**
 * register test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.26
 *
 * @version 1.0
 */
class RegisterTest extends TestCase
{
    public function testBaseUse()
    {
        $test = new Register($container = $this->createContainer());

        $test->register();

        $manager = $container->make('caches');

        $filePath = __DIR__.'/cache/hello.php';

        $this->assertFileNotExists($filePath);

        $manager->set('hello', 'world');

        $this->assertFileExists($filePath);

        $this->assertSame('world', $manager->get('hello'));

        $manager->delete('hello');

        $this->assertFileNotExists($filePath);

        $this->assertFalse($manager->get('hello'));

        Fso::deleteDirectory(__DIR__.'/cache', true);
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $option = new Option([
            'cache' => [
                'default'     => 'file',
                'expire'      => 86400,
                'time_preset' => [],
                'connect'     => [
                    'file' => [
                        'driver'    => 'file',
                        'path'      => __DIR__.'/cache',
                        'serialize' => true,
                        'expire'    => null,
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        return $container;
    }
}
