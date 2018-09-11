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

use Leevel\Cache\File;
use Tests\TestCase;

/**
 * file test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.05
 *
 * @version 1.0
 */
class FileTest extends TestCase
{
    protected function tearDown()
    {
        // for testWriteException
        $path = __DIR__.'/write';

        if (is_dir($path)) {
            rmdir($path);
        }

        // for testParentWriteException
        $path2 = __DIR__.'/parentWrite/sub';

        if (is_dir($path2)) {
            rmdir($path2);
            rmdir(dirname($path2));
        }

        if (is_dir(dirname($path2))) {
            rmdir(dirname($path2));
        }

        // for testGetIsNotReadable
        $filePath = __DIR__.'/cacheFile/readable.php';

        if (is_file($filePath)) {
            unlink($filePath);
        }

        $path = __DIR__.'/cacheFile';

        if (is_dir($path)) {
            rmdir($path);
        }
    }

    public function testBaseUse()
    {
        $filePath = __DIR__.'/cacheFile/hello.php';

        if (is_file($filePath)) {
            unlink($filePath);
        }

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

        $this->assertNull($file->handle());
    }

    public function testSetOption()
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $file->set('setOption', 'bar');

        $this->assertSame('bar', $file->get('setOption'));

        $filePath = __DIR__.'/cacheFile/setOption.php';

        $this->assertTrue(is_file($filePath));

        $this->assertContains('s:3:"bar"', file_get_contents($filePath));

        $file->delete('setOption');

        $file->setOption('serialize', false);

        $file->set('setOption2', 'bar');

        $this->assertSame('bar', $file->get('setOption2'));

        $filePath = __DIR__.'/cacheFile/setOption2.php';
        $this->assertTrue(is_file($filePath));

        $this->assertNotContains('s:3:"bar"', file_get_contents($filePath));

        $file->delete('setOption2');
    }

    public function testCacheTime()
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

    public function testGetNotExists()
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $this->assertFalse($file->get('notExists'));
    }

    public function testGet2()
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

    public function testGetIsNotReadable()
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

    public function testGetIsExpired()
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $this->assertFalse($file->get('isExpired'));

        $filePath = __DIR__.'/cacheFile/isExpired.php';

        $file->set('isExpired', 'bar');

        $this->assertFalse($file->get('isExpired', false, ['expire' => -100]));

        unlink($filePath);
    }

    public function testWithOption()
    {
        $file = new File([
            'path' => __DIR__.'/cacheFile',
        ]);

        $filePath = __DIR__.'/cacheFile/withOption.php';

        if (is_file($filePath)) {
            unlink($filePath);
        }

        $file->set('withOption', 'world', [
            'serialize' => true,
        ]);

        $this->assertTrue(is_file($filePath));

        $this->assertContains('s:5:"world"', file_get_contents($filePath));

        $file->set('withOption', 'world', [
            'serialize' => false,
        ]);

        $this->assertTrue(is_file($filePath));

        $this->assertNotContains('s:5:"world"', file_get_contents($filePath));

        unlink($filePath);
    }

    public function testCachePathEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cache path is not allowed empty.');

        $file = new File([
            'path' => '',
        ]);

        $file->set('hello', 'world');
    }

    public function testCachePathSub()
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

    public function testWriteException()
    {
        $path = __DIR__.'/write';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Dir %s is not writeable.', $path));

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

    public function testParentWriteException()
    {
        $path = __DIR__.'/parentWrite/sub';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Unable to create the %s directory.', $path));

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
