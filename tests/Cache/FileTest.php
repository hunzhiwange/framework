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
            __DIR__.'/write',
            __DIR__.'/cacheFile',
            __DIR__.'/parentWrite',
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

    public function testSetOption(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $file->set('setOption', 'bar');
        $this->assertSame('bar', $file->get('setOption'));

        $filePath = __DIR__.'/cacheFile/setOption.php';

        $this->assertTrue(is_file($filePath));
        $this->assertStringContainsString('[86400,', file_get_contents($filePath));

        $file->delete('setOption');
        $file->setOption('expire', 20);
        $file->set('setOption2', 'bar');

        $this->assertSame('bar', $file->get('setOption2'));

        $filePath = __DIR__.'/cacheFile/setOption2.php';
        $this->assertTrue(is_file($filePath));
        $this->assertStringContainsString('[20,', file_get_contents($filePath));

        $file->delete('setOption2');
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
        $this->assertFalse($file->get('bar'));
        $this->assertSame('helloworld1', $file->get('hello123456world'));
        $this->assertSame('helloworld2', $file->get('hello789world'));
        $this->assertFalse($file->get('foo123456bar'));
        $this->assertFalse($file->get('foo789bar'));
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

    public function testGet2(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        $this->assertFalse($file->get('get2'));

        $filePath = __DIR__.'/cacheFile/get2.php';
        if (!is_dir(__DIR__.'/cacheFile')) {
            mkdir(__DIR__.'/cacheFile', 0777);
        }

        file_put_contents($filePath, 'foo');
        $this->assertFalse($file->get('get2'));
        unlink($filePath);
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

    public function testGetIsExpired(): void
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);
        $this->assertFalse($file->get('isExpired'));

        $file->set('isExpired', 'bar', -100);
        $this->assertFalse($file->get('isExpired'));
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

    public function testWriteException(): void
    {
        $path = __DIR__.'/write';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Dir `%s` is not writeable.', $path));

        $file = new File([
            'path' => $path,
        ]);

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir($path, 0444);

        if (is_writable($path)) {
            $this->markTestSkipped('Mkdir with chmod is invalid.');
        }

        $file->set('hello', 'world');
    }

    public function testParentWriteException(): void
    {
        $path = __DIR__.'/parentWrite/sub';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Dir `%s` is not writeable.', dirname($path))
        );

        $file = new File([
            'path' => $path,
        ]);

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir(dirname($path), 0444, true);

        if (is_writable(dirname($path))) {
            $this->markTestSkipped('Mkdir with chmod is invalid.');
        }

        $file->set('hello', 'world');
    }
}
