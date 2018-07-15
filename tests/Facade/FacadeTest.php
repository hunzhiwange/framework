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

namespace Tests\Facade;

use Leevel\Di\Container;
use Leevel\Support\Facade;
use Tests\TestCase;

/**
 * facade test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.12
 *
 * @version 1.0
 */
class FacadeTest extends TestCase
{
    protected $container;

    protected function setUp()
    {
        $this->container = new Container();

        Facade::setContainer($this->container);
    }

    protected function tearDown()
    {
        Facade::setContainer(null);
    }

    /**
     * @dataProvider getBaseUseData
     *
     * @param string $facade
     * @param string $serviceName
     */
    public function testBaseUse(string $facade, string $serviceName)
    {
        $className = 'Leevel\\'.$facade;

        $test = new $className();

        $this->assertSame($this->container, $test->container());

        $this->container->singleton($serviceName, function () {
            return new Service1();
        });

        // 静态属性会保持住，可能受到其它单元测试的影响
        Facade::remove($serviceName);

        $this->assertSame(
            $facade,
            call_user_func([$className, 'hello'], $facade)
        );

        // 缓存
        $this->assertSame(
            $facade,
            call_user_func([$className, 'hello'], $facade)
        );
    }

    public function getBaseUseData()
    {
        return [
            ['Auth', 'auths'],
            ['Cache', 'caches'],
            ['CacheLoad', 'cache_load'],
            ['Cookie', 'cookie'],
            ['Database', 'databases'],
            ['Db', 'databases'],
            ['Encryption', 'encryption'],
            ['Event', 'event'],
            ['Filesystem', 'filesystems'],
            ['I18n', 'i18n'],
            ['Log', 'logs'],
            ['Mail', 'mails'],
            ['Option', 'option'],
            ['Page', 'page'],
            ['Request', 'request'],
            ['Response', 'response'],
            ['Router', 'router'],
            ['Session', 'sessions'],
            ['Throttler', 'throttler'],
            ['Url', 'url'],
            ['Validate', 'validate'],
            ['View', 'view'],
        ];
    }
}

class Service1
{
    public function hello(string $facade)
    {
        return $facade;
    }
}
