<?php

declare(strict_types=1);

namespace Tests\Cache\Redis;

use Leevel\Cache\Redis\PhpRedis;
use Redis;
use RedisException;
use Tests\TestCase;

class PhpRedisTest extends TestCase
{
    protected function setUp(): void
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension must be loaded before use.');
        }

        try {
            $this->makePhpRedis();
        } catch (RedisException) {
            $this->markTestSkipped('Redis read error on connection and ignore.');
        }
    }

    public function testBaseUse(): void
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

    public function testSet(): void
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

    public function testIncrease(): void
    {
        $phpRedis = $this->makePhpRedis();
        $this->assertFalse($phpRedis->get('testIncrease'));
        $this->assertSame(1, $phpRedis->increase('testIncrease'));
        $this->assertSame(101, $phpRedis->increase('testIncrease', 100));
        $this->assertSame(201, $phpRedis->increase('testIncrease', 100));
        $phpRedis->delete('testIncrease');
        $this->assertFalse($phpRedis->get('testIncrease'));
    }

    public function testIncreaseWithExpire(): void
    {
        $phpRedis = $this->makePhpRedis();
        $this->assertFalse($phpRedis->get('testIncreaseWithExpire'));
        $this->assertSame(1, $phpRedis->increase('testIncreaseWithExpire', 1, 1));
        $this->assertSame('1', $phpRedis->get('testIncreaseWithExpire'));

        // 0.7 秒未过期
        usleep(700000);
        $this->assertSame('1', $phpRedis->get('testIncreaseWithExpire'));

        // 1.1 就过期
        usleep(400000);
        $this->assertFalse($phpRedis->get('testIncreaseWithExpire'));
    }

    public function testIncreaseReturnFalse(): void
    {
        $phpRedis = $this->makePhpRedis();
        $this->assertFalse($phpRedis->get('testIncreaseReturnFalse'));
        $phpRedis->set('testIncreaseReturnFalse', 'world');
        $this->assertFalse($phpRedis->increase('testIncreaseReturnFalse'));
        $phpRedis->delete('testIncreaseReturnFalse');
        $this->assertFalse($phpRedis->get('testIncreaseReturnFalse'));
    }

    public function testDecrease(): void
    {
        $phpRedis = $this->makePhpRedis();
        $this->assertFalse($phpRedis->get('testDecrease'));
        $this->assertSame(-1, $phpRedis->decrease('testDecrease'));
        $this->assertSame(-101, $phpRedis->decrease('testDecrease', 100));
        $this->assertSame(-201, $phpRedis->decrease('testDecrease', 100));
        $phpRedis->delete('testDecrease');
        $this->assertFalse($phpRedis->get('testIncrease'));
    }

    public function testDecreaseWithExpire(): void
    {
        $phpRedis = $this->makePhpRedis();
        $this->assertFalse($phpRedis->get('testDecreaseWithExpire'));
        $this->assertSame(-1, $phpRedis->decrease('testDecreaseWithExpire', 1, 1));
        $this->assertSame('-1', $phpRedis->get('testDecreaseWithExpire'));

        // 0.7 秒未过期
        usleep(700000);
        $this->assertSame('-1', $phpRedis->get('testDecreaseWithExpire'));

        // 1.1 就过期
        usleep(400000);
        $this->assertFalse($phpRedis->get('testDecreaseWithExpire'));
    }

    public function testDecreaseReturnFalse(): void
    {
        $phpRedis = $this->makePhpRedis();
        $this->assertFalse($phpRedis->get('testDecreaseReturnFalse'));
        $phpRedis->set('testDecreaseReturnFalse', 'world');
        $this->assertFalse($phpRedis->decrease('testDecreaseReturnFalse'));
        $phpRedis->delete('testDecreaseReturnFalse');
        $this->assertFalse($phpRedis->get('testDecreaseReturnFalse'));
    }

    public function testHas(): void
    {
        $phpRedis = $this->makePhpRedis();

        $this->assertFalse($phpRedis->has('has'));
        $phpRedis->set('has', 'world');
        $this->assertTrue($phpRedis->has('has'));
        $phpRedis->delete('has');
    }

    public function testTtl(): void
    {
        $phpRedis = $this->makePhpRedis();

        $this->assertFalse($phpRedis->has('ttl'));
        $this->assertSame(-2, $phpRedis->ttl('ttl'));
        $phpRedis->set('ttl', 'world');
        $this->assertSame(-1, $phpRedis->ttl('ttl'));
        $phpRedis->set('ttl', 'world', 1);
        $this->assertSame(1, $phpRedis->ttl('ttl'));
        $phpRedis->set('ttl', 'world', 0);
        $this->assertSame(-1, $phpRedis->ttl('ttl'));
        $phpRedis->delete('ttl');
    }

    public function testCall(): void
    {
        $phpRedis = $this->makePhpRedis();

        $this->assertSame([], $phpRedis->keys('hello'));
        $phpRedis->set('hello', 'world');
        $this->assertSame(['hello'], $phpRedis->keys('hello'));
        $phpRedis->delete('hello');
    }

    public function testSelect(): void
    {
        $phpRedis = $this->makePhpRedis([
            'select' => 1,
        ]);

        $phpRedis->set('selecttest', 'world');

        $this->assertSame('world', $phpRedis->get('selecttest'));

        $phpRedis->delete('selecttest');

        $this->assertFalse($phpRedis->get('selecttest'));
    }

    public function testWithPassword(): void
    {
        //$this->expectException(\RedisException::class);

        $phpRedis = $this->makePhpRedis([
            'password' => 'error password',
        ]);
    }

    protected function makePhpRedis(array $option = []): PhpRedis
    {
        $default = [
            'host'        => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['HOST'],
            'port'        => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PORT'],
            'password'    => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PASSWORD'],
            'select'      => 0,
            'timeout'     => 0,
            'persistent'  => false,
        ];

        $option = array_merge($default, $option);

        return new PhpRedis($option);
    }
}
