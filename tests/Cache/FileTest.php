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

        // for testGetIsNotReadable
        $filePath = __DIR__.'/_readable.php';

        if (is_file($filePath)) {
            unlink($filePath);
        }
    }

    public function testBaseUse()
    {
        $filePath = __DIR__.'/_hello.php';

        if (is_file($filePath)) {
            unlink($filePath);
        }

        $file = new File([
            'path' => __DIR__,
        ]);

        $file->set('hello', 'world');

        $this->assertTrue(is_file($filePath));

        $this->assertSame('world', $file->get('hello'));

        $file->delete('hello');

        $this->assertFalse(is_file($filePath));

        $this->assertFalse($file->get('hello'));
    }

    public function testGetNotExists()
    {
        $file = new File([
            'path' => __DIR__,
        ]);

        $this->assertFalse($file->get('notExists'));
    }

    public function testGet2()
    {
        $file = new File([
            'path' => __DIR__,
        ]);

        $this->assertFalse($file->get('get2'));

        $filePath = __DIR__.'/_get2.php';

        file_put_contents($filePath, 'foo');

        $this->assertFalse($file->get('get2'));

        unlink($filePath);
    }

    public function testGetIsNotReadable()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cache path is not readable.');

        $file = new File([
            'path' => __DIR__,
        ]);

        $this->assertFalse($file->get('readable'));

        $filePath = __DIR__.'/_readable.php';

        file_put_contents($filePath, 'foo');

        chmod($filePath, 0000);

        $this->assertFalse($file->get('readable'));
    }

    public function testGetIsExpired()
    {
        $file = new File([
            'path' => __DIR__,
        ]);

        $this->assertFalse($file->get('isExpired'));

        $filePath = __DIR__.'/_isExpired.php';

        $file->set('isExpired', 'bar');

        $this->assertFalse($file->get('isExpired', false, ['expire' => -100]));

        unlink($filePath);
    }

    public function testWithOption()
    {
        $file = new File([
            'path' => __DIR__,
        ]);

        $filePath = __DIR__.'/~@withOption.php';

        if (is_file($filePath)) {
            unlink($filePath);
        }

        $file->set('withOption', 'world', [
            'prefix' => '~@',
        ]);

        $this->assertTrue(is_file($filePath));

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

        $filePath = $path.'/_cachePathSub.php';

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

        $file->set('hello', 'world');
    }
}
