<?php

declare(strict_types=1);

namespace Tests\Session;

use Leevel\Cache\Redis as CacheRedis;
use Leevel\Cache\Redis\PhpRedis;
use Leevel\Session\ISession;
use Leevel\Session\Redis;
use RedisException;
use Tests\TestCase;

class RedisTest extends TestCase
{
    protected function setUp(): void
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension must be loaded before use.');
        }

        try {
            $this->createRedisSessionHandler();
        } catch (RedisException) {
            $this->markTestSkipped('Redis read error on connection and ignore.');
        }
    }

    public function testBaseUse(): void
    {
        $session = $this->createRedisSessionHandler();

        $this->assertInstanceof(ISession::class, $session);

        $this->assertFalse($session->isStart());
        $this->assertSame('', $session->getId());
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
    }

    public function testSave(): void
    {
        $session = $this->createRedisSessionHandler();

        $this->assertFalse($session->isStart());
        $this->assertSame('', $session->getId());
        $this->assertSame('UID', $session->getName());

        $session->start();
        $this->assertTrue($session->isStart());

        $session->save();
        $this->assertFalse($session->isStart());
    }

    public function testSaveAndStart(): void
    {
        $session = $this->createRedisSessionHandler();

        $this->assertFalse($session->isStart());
        $this->assertSame('', $session->getId());
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

        $this->assertSame('a:4:{s:5:"other";s:5:"value";s:3:"foo";s:3:"bar";s:5:"hello";s:5:"world";s:13:"flash.old.key";a:0:{}}', $session->read($sessionId));

        $session->destroySession();

        $this->assertSame('a:0:{}', $session->read($sessionId));
    }

    protected function createRedisSessionHandler(): Redis
    {
        $option = [
            'host'        => $GLOBALS['LEEVEL_ENV']['SESSION']['REDIS']['HOST'],
            'port'        => $GLOBALS['LEEVEL_ENV']['SESSION']['REDIS']['PORT'],
            'password'    => $GLOBALS['LEEVEL_ENV']['SESSION']['REDIS']['PASSWORD'],
            'select'      => 0,
            'timeout'     => 0,
            'persistent'  => false,
        ];

        return new Redis(new CacheRedis(new PhpRedis($option)));
    }
}
