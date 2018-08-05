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

namespace Tests\Http;

use Leevel\Http\File;
use Tests\TestCase;

/**
 * File test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.05
 *
 * @version 1.0
 */
class FileTest extends TestCase
{
    protected function tearDown()
    {
        $notWriteable = __DIR__.'/assert/target/notWriteable';

        if (is_dir($notWriteable)) {
            rmdir($notWriteable);
        }

        $notWriteableFile = __DIR__.'/assert/test_writeable.txt';

        if (is_file($notWriteableFile)) {
            unlink($notWriteableFile);
        }
    }

    public function testBaseUse()
    {
        $sourcePath = __DIR__.'/assert/source.txt';
        $filePath = __DIR__.'/assert/test.txt';
        $targetPath = __DIR__.'/assert/target/test.txt';

        if (is_file($filePath)) {
            unlink($filePath);
        }

        if (is_file($targetPath)) {
            unlink($targetPath);
        }

        copy($sourcePath, $filePath);

        $this->assertSame('foo', file_get_contents($filePath));

        $file = new File($filePath);

        $file->move(dirname($targetPath));

        $this->assertFileExists($targetPath);
        $this->assertFileNotExists($filePath);

        unlink($targetPath);
    }

    public function testMoveWithNewName()
    {
        $sourcePath = __DIR__.'/assert/source.txt';
        $filePath = __DIR__.'/assert/test_newname.txt';
        $targetPath = __DIR__.'/assert/target/test_newname_target.txt';

        if (is_file($filePath)) {
            unlink($filePath);
        }

        if (is_file($targetPath)) {
            unlink($targetPath);
        }

        copy($sourcePath, $filePath);

        $this->assertSame('foo', file_get_contents($filePath));

        $file = new File($filePath);

        $file->move(dirname($targetPath), 'test_newname_target.txt');

        $this->assertFileExists($targetPath);
        $this->assertFileNotExists($filePath);

        unlink($targetPath);
    }

    public function testFileNotFound()
    {
        $filePath = __DIR__.'/assert/fileNotFound.txt';

        $this->expectException(\Leevel\Http\FileNotFoundException::class);
        $this->expectExceptionMessage($filePath);

        new File($filePath);
    }

    public function testTargetDirIsNotWriteable()
    {
        $sourcePath = __DIR__.'/assert/source.txt';
        $filePath = __DIR__.'/assert/test_writeable.txt';
        $targetPath = __DIR__.'/assert/target/notWriteable/test_writeable.txt';

        $this->expectException(\Leevel\Http\FileException::class);
        $this->expectExceptionMessage(
            sprintf('Unable to write in the %s directory.', dirname($targetPath))
        );

        if (is_file($filePath)) {
            unlink($filePath);
        }

        if (is_file($targetPath)) {
            unlink($targetPath);
        }

        copy($sourcePath, $filePath);

        $this->assertSame('foo', file_get_contents($filePath));

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir(dirname($targetPath), 0444);

        $file = new File($filePath);

        $file->move(dirname($targetPath));
    }

    public function testTargetParentDirIsNotWriteable()
    {
        $sourcePath = __DIR__.'/assert/source.txt';
        $filePath = __DIR__.'/assert/test_writeable.txt';
        $targetPath = __DIR__.'/assert/target/notWriteable/sub/test_writeable.txt';

        $this->expectException(\Leevel\Http\FileException::class);
        $this->expectExceptionMessage(
            sprintf('Unable to create the %s directory.', dirname($targetPath))
        );

        if (is_file($filePath)) {
            unlink($filePath);
        }

        if (is_file($targetPath)) {
            unlink($targetPath);
        }

        copy($sourcePath, $filePath);

        $this->assertSame('foo', file_get_contents($filePath));

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir(dirname(dirname($targetPath)), 0444);

        $file = new File($filePath);

        $file->move(dirname($targetPath));
    }
}
