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

namespace Tests\Filesystem;

use Leevel\Filesystem\Helper;
use Tests\TestCase;

/**
 * @api(
 *     title="文件系统助手函数",
 *     path="component/filesystem/helper",
 *     description="",
 * )
 */
class HelperTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $dirs = [
            __DIR__.'/createFile',
            __DIR__.'/createFile2',
            __DIR__.'/createFile5',
            __DIR__.'/foo',
        ];
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                Helper::deleteDirectory($dir);
            }
        }
    }

    /**
     * @api(
     *     title="create_directory 创建目录",
     *     description="",
     *     note="",
     * )
     */
    public function testCreateDirectory(): void
    {
        $dir = __DIR__.'/createDirectory';

        $this->assertDirectoryNotExists($dir);

        Helper::createDirectory($dir);

        $this->assertDirectoryExists($dir);

        Helper::createDirectory($dir);
        Helper::createDirectory($dir);

        Helper::deleteDirectory($dir);
    }

    /**
     * @api(
     *     title="delete_directory 删除目录",
     *     description="",
     *     note="",
     * )
     */
    public function testDeleteDirectory(): void
    {
        $dir = __DIR__.'/deleteDirectory/dir';

        $this->assertDirectoryNotExists($dir);

        Helper::deleteDirectory($dir);

        Helper::createDirectory($dir);

        $this->assertDirectoryExists($dir);

        Helper::deleteDirectory($dir);

        $topDir = dirname($dir);

        $this->assertDirectoryExists($topDir);

        Helper::deleteDirectory($topDir);

        $this->assertDirectoryNotExists($topDir);
    }

    public function testDeleteDirectory2(): void
    {
        $dir = __DIR__.'/deleteDirectory2/dir';

        $topDir = dirname($dir);

        $this->assertDirectoryNotExists($dir);

        Helper::createDirectory($dir);

        $this->assertDirectoryExists($dir);

        Helper::deleteDirectory($topDir);

        $this->assertDirectoryNotExists($topDir);
    }

    /**
     * @api(
     *     title="traverse_directory 遍历目录",
     *     description="",
     *     note="",
     * )
     */
    public function testTraverseDirectory(): void
    {
        $sourcePath = __DIR__.'/traverseDirectory';
        $sourceSubPath = __DIR__.'/traverseDirectory/dir';

        $this->assertDirectoryNotExists($sourceSubPath);

        Helper::createDirectory($sourceSubPath);

        file_put_contents($testFile = $sourceSubPath.'/hello.txt', 'foo');

        $this->assertTrue(is_file($testFile));

        $this->assertSame('foo', file_get_contents($testFile));

        $filesAndDirs = [];
        $filesAndDirs2 = [];

        Helper::traverseDirectory($sourcePath, true, function ($item) use (&$filesAndDirs) {
            $filesAndDirs[] = $item->getFileName();
        });

        Helper::traverseDirectory($sourcePath, true, function ($item) use (&$filesAndDirs2) {
            $filesAndDirs2[] = $item->getFileName();
        }, ['hello.txt']);

        $this->assertSame(['dir', 'hello.txt'], $filesAndDirs);
        $this->assertSame(['dir'], $filesAndDirs2);

        Helper::deleteDirectory($sourcePath);
    }

    public function testTraverseDirectory2(): void
    {
        $sourcePath = __DIR__.'/traverseDirectory2';

        $this->assertDirectoryNotExists($sourcePath);

        Helper::traverseDirectory($sourcePath, true, function ($item) {
        });
    }

    /**
     * @api(
     *     title="tidy_path 整理目录斜线风格",
     *     description="",
     *     note="",
     * )
     */
    public function testTidyPath(): void
    {
        $sourcePath = '/home\goods/name/';

        $this->assertSame('/home/goods/name', Helper::tidyPath($sourcePath));
        $this->assertSame('\home\goods\name', Helper::tidyPath($sourcePath, false));
    }

    /**
     * @api(
     *     title="is_absolute_path 判断是否为绝对路径",
     *     description="",
     *     note="",
     * )
     */
    public function testIsAbsolutePath(): void
    {
        $this->assertTrue(Helper::isAbsolutePath('c://'));
        $this->assertTrue(Helper::isAbsolutePath('/path/hello'));
        $this->assertFalse(Helper::isAbsolutePath('hello'));
    }

    /**
     * @api(
     *     title="distributed 根据 ID 获取打散目录",
     *     description="",
     *     note="",
     * )
     */
    public function testDistributed(): void
    {
        $this->assertSame(['000/00/00/', '01'], Helper::distributed(1));

        $this->assertSame(['090/00/00/', '00'], Helper::distributed(90000000));
    }

    /**
     * @api(
     *     title="create_file 创建文件",
     *     description="",
     *     note="",
     * )
     */
    public function testCreateFile(): void
    {
        $sourcePath = __DIR__.'/createFile';
        $file = $sourcePath.'/hello.txt';

        $this->assertDirectoryNotExists($sourcePath);

        Helper::createDirectory($sourcePath);

        $this->assertFalse(is_file($file));

        Helper::createFile($file);

        $this->assertTrue(is_file($file));

        Helper::deleteDirectory($sourcePath);
    }

    public function testCreateFile2(): void
    {
        $file = __DIR__.'/HelperTest.php';

        $this->expectException(\Symfony\Component\Filesystem\Exception\IOException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to create "%s": mkdir(): File exists', $file)
        );

        Helper::createFile($file.'/demo.txt');
    }

    public function testCreateFile3(): void
    {
        $sourcePath = __DIR__.'/createFile2';
        $file = $sourcePath.'/hello2.txt';

        $this->expectException(\Symfony\Component\Filesystem\Exception\IOException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to touch "%s".', $file)
        );

        if (is_dir($sourcePath)) {
            rmdir($sourcePath);
        }

        $this->assertDirectoryNotExists($sourcePath);

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir($sourcePath, 0444);

        $this->assertFalse(is_file($file));

        Helper::createFile($file);
    }

    public function testCreateFile4(): void
    {
        $sourcePath = __DIR__.'/foo/bar/createFile4';
        $file = $sourcePath.'/hello4.txt';

        $this->assertDirectoryNotExists($sourcePath);

        $this->assertFalse(is_file($file));

        Helper::createFile($file);

        $this->assertTrue(is_file($file));

        Helper::createFile($file);

        Helper::deleteDirectory($sourcePath);
    }

    public function testCreateFile5(): void
    {
        $sourcePath = __DIR__.'/foo/bar/createFile5';
        $file = $sourcePath.'/hello5.txt';

        $this->assertDirectoryNotExists($sourcePath);

        $this->assertFalse(is_file($file));

        Helper::createFile($file, 'hello');

        $this->assertTrue(is_file($file));
        $this->assertSame('hello', file_get_contents($file));

        Helper::createFile($file, 'world', 0666);
        $this->assertSame('world', file_get_contents($file));

        Helper::deleteDirectory($sourcePath);
    }

    /**
     * @api(
     *     title="get_extension 获取上传文件扩展名",
     *     description="",
     *     note="",
     * )
     */
    public function testGetExtension(): void
    {
        $file = __DIR__.'/HelperTest.pHp';

        $this->assertSame('pHp', Helper::getExtension($file));
        $this->assertSame('PHP', Helper::getExtension($file, 1));
        $this->assertSame('php', Helper::getExtension($file, 2));
    }
}
