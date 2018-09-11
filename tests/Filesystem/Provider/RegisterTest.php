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

namespace Tests\Filesystem\Provider;

use League\Flysystem\Filesystem as LeagueFilesystem;
use Leevel\Di\Container;
use Leevel\Filesystem\Provider\Register;
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

        $manager = $container->make('filesystems');

        $path = __DIR__.'/forRegister';

        $this->assertInstanceof(LeagueFilesystem::class, $manager->getFilesystem());

        $manager->put('helloregister.txt', 'register');

        $file = $path.'/helloregister.txt';

        $this->assertTrue(is_file($file));
        $this->assertSame('register', file_get_contents($file));

        unlink($file);
        rmdir($path);
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $option = new Option([
            'filesystem' => [
                'default' => 'local',
                'connect' => [
                    'local' => [
                        'driver'  => 'local',
                        'path'    => __DIR__.'/forRegister',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        return $container;
    }
}
