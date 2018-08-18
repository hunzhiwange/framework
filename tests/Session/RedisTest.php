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

use Leevel\Cache\Cache;
use Leevel\Session\ISession;
use Leevel\Session\Redis;
use Leevel\Session\Session;
use SessionHandlerInterface;
use Tests\TestCase;

/**
 * redis test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.16
 *
 * @version 1.0
 */
class RedisTest extends TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension must be loaded before use.');
        }
    }

    public function testBaseUse()
    {
        $session = new Session($this->createRedisSessionHandler());

        $this->assertInstanceof(ISession::class, $session);

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertSame('UID', $session->getName());

        $session->start();
        $this->assertTrue($session->isStart());

        $session->set('hello', 'world');
        $this->assertSame(['hello' => 'world'], $session->all());
        $this->assertTrue($session->has('hello'));
        $this->assertSame('world', $session->get('hello'));

        $session->delete('hello');
        $this->assertSame([], $session->all());
        $this->assertFalse($session->has('hello'));
        $this->assertNull($session->get('hello'));

        $session->start();
        $this->assertTrue($session->isStart());

        $this->assertInstanceof(SessionHandlerInterface::class, $session->getConnect());
    }

    public function testGetConnect()
    {
        $session = new Session($this->createRedisSessionHandler());

        $this->assertInstanceof(SessionHandlerInterface::class, $connect = $session->getConnect());

        $this->assertInstanceof(Cache::class, $connect->getCache());

        $this->assertTrue($connect->open('', 'foo'));
        $this->assertTrue($connect->close());
        $this->assertTrue($connect->gc(0));
    }

    public function testSave()
    {
        $redis = $this->createRedisSessionHandler();

        $session = new Session($this->createRedisSessionHandler());

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertSame('UID', $session->getName());

        $session->start();
        $this->assertTrue($session->isStart());

        $session->save();
        $this->assertFalse($session->isStart());
    }

    public function testSaveAndStart()
    {
        $session = new Session($this->createRedisSessionHandler());

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertSame('UID', $session->getName());

        $session->start();
        $this->assertTrue($session->isStart());
        $this->assertSame([], $session->all());

        $session->set('foo', 'bar');
        $session->set('hello', 'world');
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $session->all());

        $session->save();
        $this->assertFalse($session->isStart());

        $session->clear();
        $this->assertSame([], $session->all());

        $session->set('other', 'value');
        $this->assertSame(['other' => 'value'], $session->all());

        $this->assertFalse($session->isStart());

        $sessionId = $session->getId();
        $session->start($sessionId);

        $this->assertTrue($session->isStart());
        $this->assertSame(['other' => 'value', 'foo' => 'bar', 'hello' => 'world', 'flash.old.key' => []], $session->all());

        $session->save();
        $this->assertFalse($session->isStart());

        $sessionId = $session->getId();

        $connect = $session->getConnect();

        $this->assertSame(['other' => 'value', 'foo' => 'bar', 'hello' => 'world', 'flash.old.key' => []], $connect->read($sessionId));

        $session->destroy();

        $this->assertSame([], $connect->read($sessionId));
    }

    protected function createRedisSessionHandler()
    {
        $option = [
            'host'        => '127.0.0.1',
            'port'        => 6379,
            'password'    => '',
            'select'      => 0,
            'timeout'     => 0,
            'persistent'  => false,
        ];

        return new Redis($option);
    }
}
