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

namespace Tests\Cache;

use Leevel\Cache\Redis;
use Leevel\Cache\Redis\IConnect;
use Tests\TestCase;

/**
 * redis test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.29
 *
 * @version 1.0
 */
class RedisTest extends TestCase
{
    public function testBaseUse()
    {
        $phpRedis = $this->createMock(IConnect::class);

        $this->assertInstanceof(IConnect::class, $phpRedis);

        $phpRedis->method('get')->willReturn(false);
        $this->assertFalse($phpRedis->get('foo'));

        $redis = $this->makeRedis($phpRedis);

        $this->assertFalse($redis->get('foo'));
    }

    public function testGet()
    {
        $phpRedis = $this->createMock(IConnect::class);

        $this->assertInstanceof(IConnect::class, $phpRedis);

        $phpRedis->method('get')->willReturn(serialize('bar'));
        $this->assertEquals(serialize('bar'), $phpRedis->get('foo'));

        $redis = $this->makeRedis($phpRedis);

        $this->assertSame('bar', $redis->get('foo'));
    }

    public function testGet2()
    {
        $phpRedis = $this->createMock(IConnect::class);

        $this->assertInstanceof(IConnect::class, $phpRedis);

        $phpRedis->method('get')->willReturn(1);
        $this->assertEquals(1, $phpRedis->get('num'));

        $redis = $this->makeRedis($phpRedis);

        $this->assertSame(1, $redis->get('num'));
    }

    public function testSet()
    {
        $phpRedis = $this->createMock(IConnect::class);

        $this->assertInstanceof(IConnect::class, $phpRedis);

        $phpRedis->method('set')->willReturn(null);
        $this->assertNull($phpRedis->set('foo', 'bar', 60));

        $redis = $this->makeRedis($phpRedis);

        $this->assertNull($redis->set('foo', 'bar', ['expire' => 60]));
    }

    public function testDelete()
    {
        $phpRedis = $this->createMock(IConnect::class);

        $this->assertInstanceof(IConnect::class, $phpRedis);

        $phpRedis->method('delete')->willReturn(null);
        $this->assertNull($phpRedis->delete('foo'));

        $redis = $this->makeRedis($phpRedis);

        $this->assertNull($redis->delete('foo'));
    }

    public function testClose()
    {
        $phpRedis = $this->createMock(IConnect::class);

        $this->assertInstanceof(IConnect::class, $phpRedis);

        $phpRedis->method('close')->willReturn(null);
        $this->assertNull($phpRedis->close());

        $redis = $this->makeRedis($phpRedis);

        $this->assertNull($redis->close());
    }

    protected function makeRedis(IConnect $phpRedis, array $option = []): Redis
    {
        $default = [
            'time_preset' => [],
            'expire'      => 86400,
            'serialize'   => true,
        ];

        $option = array_merge($default, $option);

        return new Redis($phpRedis, $option);
    }
}
