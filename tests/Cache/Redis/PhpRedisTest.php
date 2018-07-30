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

namespace Tests\Cache\Redis;

use Leevel\Cache\Redis\PhpRedis;
use Redis;
use Tests\TestCase;

/**
 * phpRedis test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.30
 *
 * @version 1.0
 */
class PhpRedisTest extends TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension must be loaded before use.');
        }
    }

    public function testBaseUse()
    {
        $phpRedis = $this->makePhpRedis();

        $phpRedis->set('hello', 'world');
        $phpRedis->set('foo', 'bar');
        $phpRedis->set('num', 123);

        $this->assertSame('world', $phpRedis->get('hello'));
        $this->assertSame('bar', $phpRedis->get('foo'));
        $this->assertSame('123', $phpRedis->get('num'));

        $phpRedis->delete('hello');
        $phpRedis->delete('foo');
        $phpRedis->delete('num');

        $this->assertFalse($phpRedis->get('hello'));
        $this->assertFalse($phpRedis->get('foo'));
        $this->assertFalse($phpRedis->get('num'));

        $this->assertInstanceof(Redis::class, $phpRedis->handle());
        $this->assertNull($phpRedis->close());
        $this->assertNull($phpRedis->handle());
    }

    public function testSet()
    {
        $phpRedis = $this->makePhpRedis();

        $phpRedis->set('testset', 'world');

        $this->assertSame('world', $phpRedis->get('testset'));

        $phpRedis->delete('testset');

        $this->assertFalse($phpRedis->get('hello'));

        $phpRedis->set('testfoo', 'bar', 1);

        $this->assertSame('bar', $phpRedis->get('testfoo'));

        // 0.7 秒未过期
        usleep(700000);

        $this->assertSame('bar', $phpRedis->get('testfoo'));

        // 1.1 就过期
        usleep(400000);

        $this->assertFalse($phpRedis->get('testfoo'));
    }

    public function testSelect()
    {
        $phpRedis = $this->makePhpRedis([
            'select' => 1,
        ]);

        $phpRedis->set('selecttest', 'world');

        $this->assertSame('world', $phpRedis->get('selecttest'));

        $phpRedis->delete('testset');

        $this->assertFalse($phpRedis->get('hello'));
    }

    public function testAuth()
    {
        $this->expectException(\RedisException::class);
        $this->expectExceptionMessage(
            'NOAUTH Authentication required.'
        );

        $phpRedis = $this->makePhpRedis([
            'select' => 1,
            'auth'   => 'error',
        ]);

        $phpRedis->set('authtest', 'world');
    }

    protected function makePhpRedis(array $option = []): PhpRedis
    {
        $default = [
            'host'        => '127.0.0.1',
            'port'        => 6379,
            'password'    => '',
            'select'      => 0,
            'timeout'     => 0,
            'persistent'  => false,
        ];

        $option = array_merge($default, $option);

        return new PhpRedis($option);
    }
}
