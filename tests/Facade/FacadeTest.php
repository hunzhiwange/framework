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
    /**
     * @dataProvider getBaseUseData
     *
     * @param string $facade
     * @param string $serviceName
     * @param string $package
     */
    public function testBaseUse(string $facade, string $serviceName, string $package)
    {
        Facade::setContainer($container = new Container());

        $className = 'Leevel\\'.$package.'\\Facade\\'.$facade;

        $test = new $className();

        $this->assertSame($container, $test->container());

        $container->singleton($serviceName, function () {
            return new Service1();
        });

        $this->assertSame(
            $facade,
            call_user_func([$className, 'hello'], $facade)
        );

        // 缓存
        $this->assertSame(
            $facade,
            call_user_func([$className, 'hello'], $facade)
        );

        // 静态属性会保持住，可能受到其它单元测试的影响
        Facade::remove($serviceName);
        Facade::setContainer(null);
    }

    public function getBaseUseData()
    {
        return [
            ['Auth', 'auths', 'Auth'],
            ['Cache', 'caches', 'Cache'],
            ['CacheLoad', 'cache.load', 'Cache'],
            ['Cookie', 'cookie', 'Cookie'],
            ['Database', 'databases', 'Database'],
            ['Db', 'databases', 'Database'],
            ['Debug', 'debug', 'Debug'],
            ['Encryption', 'encryption', 'Encryption'],
            ['Event', 'event', 'Event'],
            ['Filesystem', 'filesystems', 'Filesystem'],
            ['I18n', 'i18n', 'I18n'],
            ['Leevel', 'project', 'Kernel'],
            ['Log', 'logs', 'Log'],
            ['Mail', 'mails', 'Mail'],
            ['Option', 'option', 'Option'],
            ['Request', 'request', 'Router'],
            ['Response', 'response', 'Router'],
            ['Router', 'router', 'Router'],
            ['Session', 'sessions', 'Session'],
            ['Throttler', 'throttler', 'Throttler'],
            ['Url', 'url', 'Router'],
            ['Validate', 'validate', 'Validate'],
            ['View', 'view', 'Router'],
            ['Work', 'work', 'Database'],
            ['Pool', 'pool', 'Protocol'],
            ['Rpc', 'rpc', 'Protocol'],
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
