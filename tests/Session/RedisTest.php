<?php

declare(strict_types=1);

namespace Tests\Session;

use Leevel\Cache\Redis as CacheRedis;
use Leevel\Session\ISession;
use Leevel\Session\Redis;
use Tests\TestCase;

final class RedisTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        if (!\extension_loaded('redis')) {
            self::markTestSkipped('Redis extension must be loaded before use.');
        }

        try {
            $this->createRedisSessionHandler();
        } catch (\RedisException) {
            self::markTestSkipped('Redis read error on connection and ignore.');
        }
    }

    public function testBaseUse(): void
    {
        $session = $this->createRedisSessionHandler();

        $this->assertInstanceof(ISession::class, $session);

        self::assertFalse($session->isStart());
        self::assertSame('', $session->getId());
        self::assertSame('UID', $session->getName());

        $session->start();
        self::assertTrue($session->isStart());

        $session->set('hello', 'world');
        self::assertSame(['hello' => 'world'], $session->all());
        self::assertTrue($session->has('hello'));
        self::assertSame('world', $session->get('hello'));

        $session->delete('hello');
        self::assertSame([], $session->all());
        self::assertFalse($session->has('hello'));
        self::assertNull($session->get('hello'));

        $session->start();
        self::assertTrue($session->isStart());
    }

    public function testSave(): void
    {
        $session = $this->createRedisSessionHandler();

        self::assertFalse($session->isStart());
        self::assertSame('', $session->getId());
        self::assertSame('UID', $session->getName());

        $session->start();
        self::assertTrue($session->isStart());

        $session->save();
        self::assertFalse($session->isStart());
    }

    public function testSaveAndStart(): void
    {
        $session = $this->createRedisSessionHandler();

        self::assertFalse($session->isStart());
        self::assertSame('', $session->getId());
        self::assertSame('UID', $session->getName());

        $session->start();
        self::assertTrue($session->isStart());
        self::assertSame([], $session->all());

        $session->set('foo', 'bar');
        $session->set('hello', 'world');
        self::assertSame(['foo' => 'bar', 'hello' => 'world'], $session->all());

        $session->save();
        self::assertFalse($session->isStart());

        $session->clear();
        self::assertSame([], $session->all());

        $session->set('other', 'value');
        self::assertSame(['other' => 'value'], $session->all());

        self::assertFalse($session->isStart());

        $sessionId = $session->getId();
        $session->start($sessionId);

        self::assertTrue($session->isStart());
        self::assertSame(['other' => 'value', 'foo' => 'bar', 'hello' => 'world', 'flash.old.key' => []], $session->all());

        $session->save();
        self::assertFalse($session->isStart());

        $sessionId = $session->getId();

        self::assertSame('a:4:{s:5:"other";s:5:"value";s:3:"foo";s:3:"bar";s:5:"hello";s:5:"world";s:13:"flash.old.key";a:0:{}}', $session->read($sessionId));

        $session->destroySession();

        self::assertSame('a:0:{}', $session->read($sessionId));
    }

    protected function createRedisSessionHandler(): Redis
    {
        $config = [
            'host' => $GLOBALS['LEEVEL_ENV']['SESSION']['REDIS']['HOST'],
            'port' => $GLOBALS['LEEVEL_ENV']['SESSION']['REDIS']['PORT'],
            'password' => $GLOBALS['LEEVEL_ENV']['SESSION']['REDIS']['PASSWORD'],
            'select' => 0,
            'timeout' => 0,
            'persistent' => false,
        ];

        return new Redis(new CacheRedis($config));
    }
}
