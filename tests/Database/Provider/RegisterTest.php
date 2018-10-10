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

namespace Tests\Database\Provider;

use Leevel\Cache\ICache;
use Leevel\Database\Provider\Register;
use Leevel\Di\Container;
use Leevel\Log\ILog;
use Leevel\Option\Option;
use PDO;
use Tests\Database\Query\Query;
use Tests\TestCase;

/**
 * register test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.10
 *
 * @version 1.0
 */
class RegisterTest extends TestCase
{
    use Query;

    protected function setUp()
    {
        $this->truncate('guestbook');
    }

    protected function tearDown()
    {
        $this->setUp();
    }

    public function testBaseUse()
    {
        $test = new Register($container = $this->createContainer());

        $test->register();

        $container->alias($test->providers());

        $manager = $container->make('databases');

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $manager->
        table('guestbook')->
        insert($data));

        $result = $manager->table('guestbook', 'name,content')->

        where('id', 1)->

        findOne();

        $this->assertSame('tom', $result->name);
        $this->assertSame('I love movie.', $result->content);

        $this->truncate('guestbook');
    }

    public function testUseAlias()
    {
        $test = new Register($container = $this->createContainer());

        $test->register();

        $container->alias($test->providers());

        $manager = $container->make('Leevel\\Database\\Manager');

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $manager->
        table('guestbook')->
        insert($data));

        $result = $manager->table('guestbook', 'name,content')->

        where('id', 1)->

        findOne();

        $this->assertSame('tom', $result->name);
        $this->assertSame('I love movie.', $result->content);

        $this->truncate('guestbook');
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $option = new Option([
            'database' => [
                'default' => 'mysql',
                'fetch'   => PDO::FETCH_OBJ,
                'log'     => true,
                'connect' => [
                    'mysql' => [
                        'driver'   => 'mysql',
                        'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                        'port'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                        'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                        'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                        'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                        'charset'  => 'utf8',
                        'options'  => [
                            PDO::ATTR_PERSISTENT => false,
                        ],
                        'readwrite_separate' => false,
                        'distributed'        => false,
                        'master'             => [],
                        'slave'              => [],
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        $container->instance('log', $this->createMock(ILog::class));

        $container->instance('cache', $this->createMock(ICache::class));

        return $container;
    }
}
