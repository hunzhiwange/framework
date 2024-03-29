<?php

declare(strict_types=1);

namespace Tests\Cache;

use Leevel\Cache\File;
use Leevel\Filesystem\Helper;
use Tests\TestCase;

final class FileTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $dirs = [
            __DIR__.'/cacheFile',
        ];
        foreach ($dirs as $dir) {
            Helper::deleteDirectory($dir);
        }
    }

    public function testBaseUse(): void
    {
        $filePath = __DIR__.'/cacheFile/hello.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $file->set('hello', 'world');
        static::assertTrue(is_file($filePath));
        static::assertSame('world', $file->get('hello'));

        $file->delete('hello');
        static::assertFalse(is_file($filePath));
        static::assertFalse($file->get('hello'));
        static::assertNull($file->close());
        static::assertNull($file->close()); // 关闭多次不做任何事
    }

    public function testIncrease(): void
    {
        $filePath = __DIR__.'/cacheFile/increase.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        static::assertSame(1, $file->increase('increase'));
        static::assertTrue(is_file($filePath));
        static::assertSame(101, $file->increase('increase', 100));
    }

    public function testIncrease2(): void
    {
        $filePath = __DIR__.'/cacheFile/increase.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        static::assertSame(1, $file->increase('increase', 1, -50));
        static::assertTrue(is_file($filePath));
        static::assertSame(101, $file->increase('increase', 100));
    }

    public function testDecrease(): void
    {
        $filePath = __DIR__.'/cacheFile/decrease.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        static::assertSame(-1, $file->decrease('decrease'));
        static::assertTrue(is_file($filePath));
        static::assertSame(-101, $file->decrease('decrease', 100));
    }

    public function testIncreaseCacheDataIsInvalid(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        static::assertFalse($file->get('testIncreaseCacheDataIsInvalid'));

        $filePath = __DIR__.'/cacheFile/testIncreaseCacheDataIsInvalid.php';
        if (!is_dir(__DIR__.'/cacheFile')) {
            mkdir(__DIR__.'/cacheFile', 0o777);
        }

        file_put_contents($filePath, '<?php die(/* 2020-03-05 15:49:21  */); ?>[86400,"\\"[1583755712,\\\\\\"hello\\\\\\"]\\""]');
        static::assertFalse($file->increase('testIncreaseCacheDataIsInvalid'));
    }

    public function testHas(): void
    {
        $filePath = __DIR__.'/cacheFile/has.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        static::assertFalse($file->has('has'));
        $file->set('has', 'world');
        static::assertTrue(is_file($filePath));
        static::assertTrue($file->has('has'));
    }

    public function testTtl(): void
    {
        $filePath = __DIR__.'/cacheFile/ttl.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        static::assertFalse($file->has('ttl'));
        static::assertSame(-2, $file->ttl('ttl'));
        $file->set('ttl', 'world');
        static::assertTrue(is_file($filePath));
        static::assertSame(86400, $file->ttl('ttl'));
        $file->set('ttl', 'world', 1);
        static::assertSame(1, $file->ttl('ttl'));
        $file->set('ttl', 'world', 0);
        static::assertSame(-1, $file->ttl('ttl'));
    }

    public function testReconnect(): void
    {
        $filePath = __DIR__.'/cacheFile/hello.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        static::assertNull($file->close());
        static::assertFalse(is_file($filePath));
        static::assertFalse($file->get('hello'));

        $file->set('hello', 'world');
        static::assertTrue(is_file($filePath));
        static::assertSame('world', $file->get('hello'));
        $file->delete('hello');
    }

    public function testGetNotExists(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        static::assertFalse($file->get('notExists'));
    }

    public function testGetInvalidContent(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        static::assertFalse($file->get('testGetInvalidContent'));

        $filePath = __DIR__.'/cacheFile/testGetInvalidContent.php';
        if (!is_dir(__DIR__.'/cacheFile')) {
            mkdir(__DIR__.'/cacheFile', 0o777);
        }

        file_put_contents($filePath, 'foo');
        static::assertFalse($file->get('testGetInvalidContent'));
        unlink($filePath);
    }

    public function testGetInvalidContentNotIsArray(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        static::assertFalse($file->get('testGetInvalidContentNotIsArray'));

        $filePath = __DIR__.'/cacheFile/testGetInvalidContentNotIsArray.php';
        if (!is_dir(__DIR__.'/cacheFile')) {
            mkdir(__DIR__.'/cacheFile', 0o777);
        }

        file_put_contents($filePath, '<?php die(/* 2020-03-05 15:49:21  */); ?>11');
        static::assertFalse($file->get('testGetInvalidContentNotIsArray'));
        unlink($filePath);
    }

    public function testGetWithException(): void
    {
        $this->expectException(\JsonException::class);
        $this->expectExceptionMessage(
            'Syntax error'
        );

        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        static::assertFalse($file->get('testGetWithException'));

        $filePath = __DIR__.'/cacheFile/testGetWithException.php';
        if (!is_dir(__DIR__.'/cacheFile')) {
            mkdir(__DIR__.'/cacheFile', 0o777);
        }

        file_put_contents($filePath, '<?php die(/* 2020-03-05 15:49:21  */); ?>[11..,22]');
        static::assertFalse($file->get('testGetWithException'));
    }

    public function testGetWithNotExpire(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        static::assertFalse($file->get('testGetWithNotExpire'));

        $filePath = __DIR__.'/cacheFile/testGetWithNotExpire.php';
        if (!is_dir(__DIR__.'/cacheFile')) {
            mkdir(__DIR__.'/cacheFile', 0o777);
        }

        file_put_contents($filePath, '<?php die(/* 2020-03-05 15:49:21  */); ?>[-2,"22"]');
        static::assertSame(22, $file->get('testGetWithNotExpire'));
    }

    public function testGetIsNotReadable(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cache path is not readable.');

        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        static::assertFalse($file->get('readable'));

        $filePath = __DIR__.'/cacheFile/readable.php';
        if (!is_dir(__DIR__.'/cacheFile')) {
            mkdir(__DIR__.'/cacheFile', 0o777);
        }

        file_put_contents($filePath, 'foo');
        chmod($filePath, 0);

        if (is_readable($filePath)) {
            static::markTestSkipped('Chmod is invalid.');
        }

        static::assertFalse($file->get('readable'));
    }

    public function testGetIsNotExpired(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        static::assertFalse($file->get('isNotExpired'));

        $file->set('isNotExpired', 'bar', -100);
        static::assertSame('bar', $file->get('isNotExpired'));
    }

    public function testSetExpire(): void
    {
        $filePath = __DIR__.'/cacheFile/withConfig.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $file->set('withConfig', 'world', 111);

        static::assertTrue(is_file($filePath));
        static::assertStringContainsString('[111,', file_get_contents($filePath));

        $file->set('withConfig', 'world', 222);

        static::assertTrue(is_file($filePath));
        static::assertStringNotContainsString('s:5:"world"', file_get_contents($filePath));
    }

    public function testGetAndIsExpired(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        static::assertFalse($file->get('testGetAndIsExpired'));
        $file->set('testGetAndIsExpired', 'hello', 2);
        static::assertSame('hello', $file->get('testGetAndIsExpired'));

        // 1 秒未过期
        sleep(1);
        static::assertSame('hello', $file->get('testGetAndIsExpired'));

        // 4 秒就过期
        sleep(3);
        static::assertFalse($file->get('testGetAndIsExpired'));
    }

    public function testCachePathEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cache path is not allowed empty.');

        $file = new File([
            'path' => '',
        ]);

        $file->set('hello', 'world');
    }

    public function testCachePathSub(): void
    {
        $file = new File([
            'path' => $path = __DIR__.'/sub',
        ]);

        $file->set('cachePathSub', 'world');
        $filePath = $path.'/cachePathSub.php';
        static::assertTrue(is_file($filePath));

        unlink($filePath);
        rmdir($path);
    }

    public function testCacheKeySplitAsDir(): void
    {
        $filePath = __DIR__.'/cacheFile/hello/world/foo/bar.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $file->set('hello:world:foo:bar', 1);
        static::assertTrue(is_file($filePath));
        static::assertSame(1, $file->get('hello:world:foo:bar'));
    }
}
