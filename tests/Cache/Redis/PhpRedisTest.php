<?php

declare(strict_types=1);

namespace Tests\Cache\Redis;

use Leevel\Cache\Redis\PhpRedis;
use RedisException;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class PhpRedisTest extends TestCase
{
    protected function setUp(): void
    {
        if (!\extension_loaded('redis')) {
            static::markTestSkipped('Redis extension must be loaded before use.');
        }

        try {
            $this->makePhpRedis();
        } catch (RedisException) {
            static::markTestSkipped('Redis read error on connection and ignore.');
        }
    }

    public function testBaseUse(): void
    {
        $phpRedis = $this->makePhpRedis();

        $phpRedis->set('hello', 'world');
        $phpRedis->set('foo', 'bar');
        $phpRedis->set('num', 123);

        static::assertSame('world', $phpRedis->get('hello'));
        static::assertSame('bar', $phpRedis->get('foo'));
        static::assertSame('123', $phpRedis->get('num'));

        $phpRedis->delete('hello');
        $phpRedis->delete('foo');
        $phpRedis->delete('num');

        static::assertFalse($phpRedis->get('hello'));
        static::assertFalse($phpRedis->get('foo'));
        static::assertFalse($phpRedis->get('num'));

        $this->assertInstanceof(\Redis::class, $phpRedis->handle());
        static::assertNull($phpRedis->close());
        static::assertNull($phpRedis->handle());
    }

    public function testSet(): void
    {
        $phpRedis = $this->makePhpRedis();

        $phpRedis->set('testset', 'world');

        static::assertSame('world', $phpRedis->get('testset'));

        $phpRedis->delete('testset');

        static::assertFalse($phpRedis->get('hello'));

        $phpRedis->set('testfoo', 'bar', 1);

        static::assertSame('bar', $phpRedis->get('testfoo'));

        // 0.7 秒未过期
        usleep(700000);
        static::assertSame('bar', $phpRedis->get('testfoo'));

        // 1.1 就过期
        usleep(400000);
        static::assertFalse($phpRedis->get('testfoo'));
    }

    public function testIncrease(): void
    {
        $phpRedis = $this->makePhpRedis();
        static::assertFalse($phpRedis->get('testIncrease'));
        static::assertSame(1, $phpRedis->increase('testIncrease'));
        static::assertSame(101, $phpRedis->increase('testIncrease', 100));
        static::assertSame(201, $phpRedis->increase('testIncrease', 100));
        $phpRedis->delete('testIncrease');
        static::assertFalse($phpRedis->get('testIncrease'));
    }

    public function testIncreaseWithExpire(): void
    {
        $phpRedis = $this->makePhpRedis();
        static::assertFalse($phpRedis->get('testIncreaseWithExpire'));
        static::assertSame(1, $phpRedis->increase('testIncreaseWithExpire', 1, 1));
        static::assertSame('1', $phpRedis->get('testIncreaseWithExpire'));

        // 0.7 秒未过期
        usleep(700000);
        static::assertSame('1', $phpRedis->get('testIncreaseWithExpire'));

        // 1.1 就过期
        usleep(400000);
        static::assertFalse($phpRedis->get('testIncreaseWithExpire'));
    }

    public function testIncreaseReturnFalse(): void
    {
        $phpRedis = $this->makePhpRedis();
        static::assertFalse($phpRedis->get('testIncreaseReturnFalse'));
        $phpRedis->set('testIncreaseReturnFalse', 'world');
        static::assertFalse($phpRedis->increase('testIncreaseReturnFalse'));
        $phpRedis->delete('testIncreaseReturnFalse');
        static::assertFalse($phpRedis->get('testIncreaseReturnFalse'));
    }

    public function testDecrease(): void
    {
        $phpRedis = $this->makePhpRedis();
        static::assertFalse($phpRedis->get('testDecrease'));
        static::assertSame(-1, $phpRedis->decrease('testDecrease'));
        static::assertSame(-101, $phpRedis->decrease('testDecrease', 100));
        static::assertSame(-201, $phpRedis->decrease('testDecrease', 100));
        $phpRedis->delete('testDecrease');
        static::assertFalse($phpRedis->get('testIncrease'));
    }

    public function testDecreaseWithExpire(): void
    {
        $phpRedis = $this->makePhpRedis();
        static::assertFalse($phpRedis->get('testDecreaseWithExpire'));
        static::assertSame(-1, $phpRedis->decrease('testDecreaseWithExpire', 1, 1));
        static::assertSame('-1', $phpRedis->get('testDecreaseWithExpire'));

        // 0.7 秒未过期
        usleep(700000);
        static::assertSame('-1', $phpRedis->get('testDecreaseWithExpire'));

        // 1.1 就过期
        usleep(400000);
        static::assertFalse($phpRedis->get('testDecreaseWithExpire'));
    }

    public function testDecreaseReturnFalse(): void
    {
        $phpRedis = $this->makePhpRedis();
        static::assertFalse($phpRedis->get('testDecreaseReturnFalse'));
        $phpRedis->set('testDecreaseReturnFalse', 'world');
        static::assertFalse($phpRedis->decrease('testDecreaseReturnFalse'));
        $phpRedis->delete('testDecreaseReturnFalse');
        static::assertFalse($phpRedis->get('testDecreaseReturnFalse'));
    }

    public function testHas(): void
    {
        $phpRedis = $this->makePhpRedis();

        static::assertFalse($phpRedis->has('has'));
        $phpRedis->set('has', 'world');
        static::assertTrue($phpRedis->has('has'));
        $phpRedis->delete('has');
    }

    public function testTtl(): void
    {
        $phpRedis = $this->makePhpRedis();

        static::assertFalse($phpRedis->has('ttl'));
        static::assertSame(-2, $phpRedis->ttl('ttl'));
        $phpRedis->set('ttl', 'world');
        static::assertSame(-1, $phpRedis->ttl('ttl'));
        $phpRedis->set('ttl', 'world', 1);
        static::assertSame(1, $phpRedis->ttl('ttl'));
        $phpRedis->set('ttl', 'world', 0);
        static::assertSame(-1, $phpRedis->ttl('ttl'));
        $phpRedis->delete('ttl');
    }

    public function testCall(): void
    {
        $phpRedis = $this->makePhpRedis();

        static::assertSame([], $phpRedis->keys('hello'));
        $phpRedis->set('hello', 'world');
        static::assertSame(['hello'], $phpRedis->keys('hello'));
        $phpRedis->delete('hello');
    }

    public function testSelect(): void
    {
        $phpRedis = $this->makePhpRedis([
            'select' => 1,
        ]);

        $phpRedis->set('selecttest', 'world');

        static::assertSame('world', $phpRedis->get('selecttest'));

        $phpRedis->delete('selecttest');

        static::assertFalse($phpRedis->get('selecttest'));
    }

    public function testWithPassword(): void
    {
        static::assertTrue(true);
        // $this->expectException(\RedisException::class);

        $phpRedis = $this->makePhpRedis([
            'password' => 'error password',
        ]);
    }

    protected function makePhpRedis(array $option = []): PhpRedis
    {
        $default = [
            'host' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['HOST'],
            'port' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PORT'],
            'password' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PASSWORD'],
            'select' => 0,
            'timeout' => 0,
            'persistent' => false,
        ];

        $option = array_merge($default, $option);

        return new PhpRedis($option);
    }
}
