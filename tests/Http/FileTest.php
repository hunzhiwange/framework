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

namespace Tests\Http;

use Leevel\Filesystem\Helper;
use Leevel\Http\File;
use Tests\TestCase;

/**
 * @api(
 *     title="File",
 *     path="component/http/file",
 *     description="QueryPHP 提供了一个文件管理 `\Leevel\Http\File` 对象。",
 * )
 */
class FileTest extends TestCase
{
    protected function tearDown(): void
    {
        $notWriteable = __DIR__.'/assert/target/notWriteable';
        if (is_dir($notWriteable)) {
            Helper::deleteDirectory($notWriteable, true);
        }

        $notWriteableFile = __DIR__.'/assert/test_writeable.txt';
        if (is_file($notWriteableFile)) {
            unlink($notWriteableFile);
        }
    }

    /**
     * @api(
     *     title="文件基本使用方法",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
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

    /**
     * @api(
     *     title="move 移动文件",
     *     description="",
     *     note="",
     * )
     */
    public function testMoveWithNewName(): void
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

    public function testFileNotFound(): void
    {
        $filePath = __DIR__.'/assert/fileNotFound.txt';

        $this->expectException(\Leevel\Http\FileNotFoundException::class);
        $this->expectExceptionMessage($filePath);

        new File($filePath);
    }

    public function testTargetDirIsNotWriteable(): void
    {
        $sourcePath = __DIR__.'/assert/source.txt';
        $filePath = __DIR__.'/assert/test_writeable.txt';
        $targetPath = __DIR__.'/assert/target/notWriteable/test_writeable.txt';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Dir `%s` is not writeable.', dirname($targetPath))
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

        if (is_writable(dirname($targetPath))) {
            $this->markTestSkipped('Mkdir with chmod is invalid.');
        }

        $file = new File($filePath);

        $file->move(dirname($targetPath));
    }

    public function testTargetParentDirIsNotWriteable(): void
    {
        $sourcePath = __DIR__.'/assert/source.txt';
        $filePath = __DIR__.'/assert/test_writeable.txt';
        $targetPath = __DIR__.'/assert/target/notWriteable/sub/test_writeable.txt';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Dir `%s` is not writeable.', dirname(dirname($targetPath)))
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

        if (is_writable(dirname(dirname($targetPath)))) {
            $this->markTestSkipped('Mkdir with chmod is invalid.');
        }

        $file = new File($filePath);

        $file->move(dirname($targetPath));
    }

    /**
     * @api(
     *     title="move 移动文件到子目录",
     *     description="",
     *     note="",
     * )
     */
    public function testMoveWithSub(): void
    {
        $sourcePath = __DIR__.'/assert/source.txt';
        $filePath = __DIR__.'/assert/test_sub.txt';
        $targetPath = __DIR__.'/assert/target/sub/test_sub.txt';

        if (is_file($filePath)) {
            unlink($filePath);
        }

        if (is_file($targetPath)) {
            unlink($targetPath);
        }

        copy($sourcePath, $filePath);

        $this->assertSame('foo', file_get_contents($filePath));

        $file = new File($filePath);

        $file->move(dirname($targetPath), 'test_sub.txt');

        $this->assertFileExists($targetPath);
        $this->assertFileNotExists($filePath);

        unlink($targetPath);
        rmdir(dirname($targetPath));
    }

    public function testMoveRenameException(): void
    {
        $sourcePath = __DIR__.'/assert/source.txt';
        $filePath = __DIR__.'/assert/test_rename.txt';
        $targetPath = __DIR__.'/assert/target/test_rename.txt';

        $this->expectException(\Leevel\Http\FileException::class);
        $this->expectExceptionMessage(
            sprintf('rename(%s,%s): No such file or directory', $filePath, $targetPath)
        );

        if (is_file($filePath)) {
            unlink($filePath);
        }

        if (is_file($targetPath)) {
            unlink($targetPath);
        }

        copy($sourcePath, $filePath);

        $this->assertSame('foo', file_get_contents($filePath));

        $file = new File($filePath);

        unlink($filePath);

        $file->move(dirname($targetPath), 'test_rename.txt');
    }
}
