<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use Leevel\Filesystem\Helper;
use Leevel\Kernel\Utils\Api;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '文件系统助手函数',
    'path' => 'component/filesystem/helper',
])]
final class HelperTest extends TestCase
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

    #[Api([
        'zh-CN:title' => 'create_directory 创建目录',
    ])]
    public function testCreateDirectory(): void
    {
        $dir = __DIR__.'/createDirectory';

        static::assertDirectoryDoesNotExist($dir);

        Helper::createDirectory($dir);

        static::assertDirectoryExists($dir);

        Helper::createDirectory($dir);
        Helper::createDirectory($dir);

        Helper::deleteDirectory($dir);
    }

    #[Api([
        'zh-CN:title' => 'delete_directory 删除目录',
    ])]
    public function testDeleteDirectory(): void
    {
        $dir = __DIR__.'/deleteDirectory/dir';

        static::assertDirectoryDoesNotExist($dir);

        Helper::deleteDirectory($dir);

        Helper::createDirectory($dir);

        static::assertDirectoryExists($dir);

        Helper::deleteDirectory($dir);

        $topDir = \dirname($dir);

        static::assertDirectoryExists($topDir);

        Helper::deleteDirectory($topDir);

        static::assertDirectoryDoesNotExist($topDir);
    }

    public function testDeleteDirectory2(): void
    {
        $dir = __DIR__.'/deleteDirectory2/dir';

        $topDir = \dirname($dir);

        static::assertDirectoryDoesNotExist($dir);

        Helper::createDirectory($dir);

        static::assertDirectoryExists($dir);

        Helper::deleteDirectory($topDir);

        static::assertDirectoryDoesNotExist($topDir);
    }

    #[Api([
        'zh-CN:title' => 'traverse_directory 遍历目录',
    ])]
    public function testTraverseDirectory(): void
    {
        $sourcePath = __DIR__.'/traverseDirectory';
        $sourceSubPath = __DIR__.'/traverseDirectory/dir';

        static::assertDirectoryDoesNotExist($sourceSubPath);

        Helper::createDirectory($sourceSubPath);

        file_put_contents($testFile = $sourceSubPath.'/hello.txt', 'foo');

        static::assertTrue(is_file($testFile));

        static::assertSame('foo', file_get_contents($testFile));

        $filesAndDirs = [];
        $filesAndDirs2 = [];

        Helper::traverseDirectory($sourcePath, true, function ($item) use (&$filesAndDirs): void {
            $filesAndDirs[] = $item->getFileName();
        });

        Helper::traverseDirectory($sourcePath, true, function ($item) use (&$filesAndDirs2): void {
            $filesAndDirs2[] = $item->getFileName();
        }, ['hello.txt']);

        static::assertSame(['dir', 'hello.txt'], $filesAndDirs);
        static::assertSame(['dir'], $filesAndDirs2);

        Helper::deleteDirectory($sourcePath);
    }

    public function testTraverseDirectory2(): void
    {
        $sourcePath = __DIR__.'/traverseDirectory2';

        static::assertDirectoryDoesNotExist($sourcePath);

        Helper::traverseDirectory($sourcePath, true, function ($item): void {
        });
    }

    #[Api([
        'zh-CN:title' => 'tidy_path 整理目录斜线风格',
    ])]
    public function testTidyPath(): void
    {
        $sourcePath = '/home\goods/name/';

        static::assertSame('/home/goods/name', Helper::tidyPath($sourcePath));
        static::assertSame('\home\goods\name', Helper::tidyPath($sourcePath, false));
    }

    #[Api([
        'zh-CN:title' => 'is_absolute_path 判断是否为绝对路径',
    ])]
    public function testIsAbsolutePath(): void
    {
        static::assertTrue(Helper::isAbsolutePath('c://'));
        static::assertTrue(Helper::isAbsolutePath('/path/hello'));
        static::assertFalse(Helper::isAbsolutePath('hello'));
    }

    #[Api([
        'zh-CN:title' => 'distributed 根据 ID 获取打散目录',
    ])]
    public function testDistributed(): void
    {
        static::assertSame(['000/00/00/', '01'], Helper::distributed(1));

        static::assertSame(['090/00/00/', '00'], Helper::distributed(90000000));
    }

    #[Api([
        'zh-CN:title' => 'create_file 创建文件',
    ])]
    public function testCreateFile(): void
    {
        $sourcePath = __DIR__.'/createFile';
        $file = $sourcePath.'/hello.txt';

        static::assertDirectoryDoesNotExist($sourcePath);

        Helper::createDirectory($sourcePath);

        static::assertFalse(is_file($file));

        Helper::createFile($file);

        static::assertTrue(is_file($file));

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
            sprintf('Failed to touch "%s"', $file)
        );

        if (is_dir($sourcePath)) {
            rmdir($sourcePath);
        }

        static::assertDirectoryDoesNotExist($sourcePath);

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir($sourcePath, 0o444);

        static::assertFalse(is_file($file));

        Helper::createFile($file);
    }

    public function testCreateFile4(): void
    {
        $sourcePath = __DIR__.'/foo/bar/createFile4';
        $file = $sourcePath.'/hello4.txt';

        static::assertDirectoryDoesNotExist($sourcePath);

        static::assertFalse(is_file($file));

        Helper::createFile($file);

        static::assertTrue(is_file($file));

        Helper::createFile($file);

        Helper::deleteDirectory($sourcePath);
    }

    public function testCreateFile5(): void
    {
        $sourcePath = __DIR__.'/foo/bar/createFile5';
        $file = $sourcePath.'/hello5.txt';

        static::assertDirectoryDoesNotExist($sourcePath);

        static::assertFalse(is_file($file));

        Helper::createFile($file, 'hello');

        static::assertTrue(is_file($file));
        static::assertSame('hello', file_get_contents($file));

        Helper::createFile($file, 'world', 0o666);
        static::assertSame('world', file_get_contents($file));

        Helper::deleteDirectory($sourcePath);
    }

    #[Api([
        'zh-CN:title' => 'get_extension 获取上传文件扩展名',
    ])]
    public function testGetExtension(): void
    {
        $file = __DIR__.'/HelperTest.pHp';

        static::assertSame('pHp', Helper::getExtension($file));
        static::assertSame('PHP', Helper::getExtension($file, 1));
        static::assertSame('php', Helper::getExtension($file, 2));
    }

    public function testNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Class "Leevel\\Filesystem\\Helper\\NotFound" not found');

        static::assertTrue(Helper::notFound());
    }
}
