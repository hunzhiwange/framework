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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Cache;

use Leevel\Cache\File;
use Leevel\Filesystem\Helper;
use Tests\TestCase;

class FileTest extends TestCase
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
            Helper::deleteDirectory($dir, true);
        }
    }

    public function testBaseUse(): void
    {
        $filePath = __DIR__.'/cacheFile/hello.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $file->set('hello', 'world');
        $this->assertTrue(is_file($filePath));
        $this->assertSame('world', $file->get('hello'));

        $file->delete('hello');
        $this->assertFalse(is_file($filePath));
        $this->assertFalse($file->get('hello'));
        $this->assertNull($file->close());
        $this->assertNull($file->close()); // 关闭多次不做任何事
        $this->assertNull($file->handle());
    }

    public function testIncrease(): void
    {
        $filePath = __DIR__.'/cacheFile/increase.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $this->assertSame(1, $file->increase('increase'));
        $this->assertTrue(is_file($filePath));
        $this->assertSame(101, $file->increase('increase', 100));
    }

    public function testDecrease(): void
    {
        $filePath = __DIR__.'/cacheFile/decrease.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $this->assertSame(-1, $file->decrease('decrease'));
        $this->assertTrue(is_file($filePath));
        $this->assertSame(-101, $file->decrease('decrease', 100));
    }

    public function testIncreaseCacheDataIsInvalid(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        $this->assertFalse($file->get('testIncreaseCacheDataIsInvalid'));

        $filePath = __DIR__.'/cacheFile/testIncreaseCacheDataIsInvalid.php';
        if (!is_dir(__DIR__.'/cacheFile')) {
            mkdir(__DIR__.'/cacheFile', 0777);
        }

        file_put_contents($filePath, '<?php die(/* 2020-03-05 15:49:21  */); ?>[86400,"\\"[1583755712,\\\\\\"hello\\\\\\"]\\""]');
        $this->assertFalse($file->increase('testIncreaseCacheDataIsInvalid'));
    }

    public function testHas(): void
    {
        $filePath = __DIR__.'/cacheFile/has.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $this->assertFalse($file->has('has'));
        $file->set('has', 'world');
        $this->assertTrue(is_file($filePath));
        $this->assertTrue($file->has('has'));
    }

    public function testTtl(): void
    {
        $filePath = __DIR__.'/cacheFile/ttl.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $this->assertFalse($file->has('ttl'));
        $this->assertSame(-2, $file->ttl('ttl'));
        $file->set('ttl', 'world');
        $this->assertTrue(is_file($filePath));
        $this->assertSame(86400, $file->ttl('ttl'));
        $file->set('ttl', 'world', 1);
        $this->assertSame(1, $file->ttl('ttl'));
        $file->set('ttl', 'world', 0);
        $this->assertSame(-1, $file->ttl('ttl'));
    }

    public function testReconnect(): void
    {
        $filePath = __DIR__.'/cacheFile/hello.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        $this->assertNull($file->close());
        $this->assertFalse(is_file($filePath));
        $this->assertFalse($file->get('hello'));

        $file->set('hello', 'world');
        $this->assertTrue(is_file($filePath));
        $this->assertSame('world', $file->get('hello'));
        $file->delete('hello');
    }

    public function testCacheTime(): void
    {
        $file = new File([
            'time_preset' => [
                'foo'         => 500,
                'bar'         => -10,
                'hello*world' => 10,
                'foo*bar'     => -10,
            ],
            'path' => __DIR__.'/cacheFile',
        ]);

        $file->set('foo', 'bar');
        $file->set('bar', 'hello');
        $file->set('hello123456world', 'helloworld1');
        $file->set('hello789world', 'helloworld2');
        $file->set('foo123456bar', 'foobar1');
        $file->set('foo789bar', 'foobar2');
        $file->set('haha', 'what about others?');

        $this->assertSame('bar', $file->get('foo'));
        $this->assertSame('hello', $file->get('bar'));
        $this->assertSame('helloworld1', $file->get('hello123456world'));
        $this->assertSame('helloworld2', $file->get('hello789world'));
        $this->assertSame('foobar1', $file->get('foo123456bar'));
        $this->assertSame('foobar2', $file->get('foo789bar'));
        $this->assertSame('what about others?', $file->get('haha'));

        $file->delete('foo');
        $file->delete('bar');
        $file->delete('hello123456world');
        $file->delete('hello789world');
        $file->delete('foo123456bar');
        $file->delete('foo789bar');
        $file->delete('haha');
    }

    public function testGetNotExists(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $this->assertFalse($file->get('notExists'));
    }

    public function testGetInvalidContent(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        $this->assertFalse($file->get('testGetInvalidContent'));

        $filePath = __DIR__.'/cacheFile/testGetInvalidContent.php';
        if (!is_dir(__DIR__.'/cacheFile')) {
            mkdir(__DIR__.'/cacheFile', 0777);
        }

        file_put_contents($filePath, 'foo');
        $this->assertFalse($file->get('testGetInvalidContent'));
        unlink($filePath);
    }

    public function testGetInvalidContentNotIsArray(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        $this->assertFalse($file->get('testGetInvalidContentNotIsArray'));

        $filePath = __DIR__.'/cacheFile/testGetInvalidContentNotIsArray.php';
        if (!is_dir(__DIR__.'/cacheFile')) {
            mkdir(__DIR__.'/cacheFile', 0777);
        }

        file_put_contents($filePath, '<?php die(/* 2020-03-05 15:49:21  */); ?>11');
        $this->assertFalse($file->get('testGetInvalidContentNotIsArray'));
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
        $this->assertFalse($file->get('testGetWithException'));

        $filePath = __DIR__.'/cacheFile/testGetWithException.php';
        if (!is_dir(__DIR__.'/cacheFile')) {
            mkdir(__DIR__.'/cacheFile', 0777);
        }

        file_put_contents($filePath, '<?php die(/* 2020-03-05 15:49:21  */); ?>[11..,22]');
        $this->assertFalse($file->get('testGetWithException'));
    }

    public function testGetWithNotExpire(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        $this->assertFalse($file->get('testGetWithNotExpire'));

        $filePath = __DIR__.'/cacheFile/testGetWithNotExpire.php';
        if (!is_dir(__DIR__.'/cacheFile')) {
            mkdir(__DIR__.'/cacheFile', 0777);
        }

        file_put_contents($filePath, '<?php die(/* 2020-03-05 15:49:21  */); ?>[-2,"22"]');
        $this->assertSame(22, $file->get('testGetWithNotExpire'));
    }

    public function testGetIsNotReadable(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cache path is not readable.');

        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        $this->assertFalse($file->get('readable'));

        $filePath = __DIR__.'/cacheFile/readable.php';
        if (!is_dir(__DIR__.'/cacheFile')) {
            mkdir(__DIR__.'/cacheFile', 0777);
        }

        file_put_contents($filePath, 'foo');
        chmod($filePath, 0000);

        if (is_readable($filePath)) {
            $this->markTestSkipped('Chmod is invalid.');
        }

        $this->assertFalse($file->get('readable'));
    }

    public function testGetIsNotExpired(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        $this->assertFalse($file->get('isNotExpired'));

        $file->set('isNotExpired', 'bar', -100);
        $this->assertSame('bar', $file->get('isNotExpired'));
    }

    public function testSetExpire(): void
    {
        $filePath = __DIR__.'/cacheFile/withOption.php';
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $file->set('withOption', 'world', 111);

        $this->assertTrue(is_file($filePath));
        $this->assertStringContainsString('[111,', file_get_contents($filePath));

        $file->set('withOption', 'world', 222);

        $this->assertTrue(is_file($filePath));
        $this->assertStringNotContainsString('s:5:"world"', file_get_contents($filePath));
    }

    public function testGetAndIsExpired(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        $this->assertFalse($file->get('testGetAndIsExpired'));
        $file->set('testGetAndIsExpired', 'hello', 1);
        $this->assertSame('hello', $file->get('testGetAndIsExpired'));

        // 1 秒未过期
        sleep(1);
        $this->assertSame('hello', $file->get('testGetAndIsExpired'));

        // 3 秒就过期
        sleep(2);
        $this->assertFalse($file->get('testGetAndIsExpired'));
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
        $this->assertTrue(is_file($filePath));

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
        $this->assertTrue(is_file($filePath));
        $this->assertSame(1, $file->get('hello:world:foo:bar'));
    }
}
