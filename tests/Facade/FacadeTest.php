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

use Leevel\Leevel\App;
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
        $container = App::singletons();
        $container->clear();

        $className = 'Leevel\\'.$package.'\\Facade\\'.$facade;

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

        $container->clear();
    }

    public function getBaseUseData()
    {
        return [
            ['Auth', 'auths', 'Auth'],
            ['Cache', 'caches', 'Cache'],
            ['CacheLoad', 'cache.load', 'Cache'],
            ['Database', 'databases', 'Database'],
            ['Db', 'databases', 'Database'],
            ['Debug', 'debug', 'Debug'],
            ['Encryption', 'encryption', 'Encryption'],
            ['Event', 'event', 'Event'],
            ['Filesystem', 'filesystems', 'Filesystem'],
            ['I18n', 'i18n', 'I18n'],
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

    public function testApp()
    {
        $container = App::singletons();
        $container->clear();

        $className = 'Leevel\\Kernel\\Facade\\App';

        $this->assertSame(
            '/foo',
            call_user_func([$className, 'path'], 'foo')
        );

        $container->clear();
    }
}

class Service1
{
    public function hello(string $facade): string
    {
        return $facade;
    }
}
