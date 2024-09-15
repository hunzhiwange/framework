<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use Leevel\Filesystem\Helper\CreateDirectory;
use Leevel\Filesystem\Helper\CreateFile;
use Leevel\Filesystem\Helper\DeleteDirectory;
use Leevel\Filesystem\Helper\Distributed;
use Leevel\Filesystem\Helper\Extension;
use Leevel\Filesystem\Helper\IsAbsolutePath;
use Leevel\Filesystem\Helper\TidyPath;
use Leevel\Filesystem\Helper\TraverseDirectory;
use Leevel\Kernel\Utils\Api;
use Symfony\Component\Filesystem\Exception\IOException;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '文件系统助手函数',
    'path' => 'component/filesystem/helper',
])]
final class HelperTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        $this->tearDown();
    }

    #[\Override]
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
                DeleteDirectory::handle($dir);
            }
        }
    }

    #[Api([
        'zh-CN:title' => 'create_directory 创建目录',
    ])]
    public function testCreateDirectory(): void
    {
        $dir = __DIR__.'/createDirectory';

        self::assertDirectoryDoesNotExist($dir);

        CreateDirectory::handle($dir);

        self::assertDirectoryExists($dir);

        CreateDirectory::handle($dir);
        CreateDirectory::handle($dir);

        DeleteDirectory::handle($dir);
    }

    #[Api([
        'zh-CN:title' => 'delete_directory 删除目录',
    ])]
    public function testDeleteDirectory(): void
    {
        $dir = __DIR__.'/deleteDirectory/dir';

        self::assertDirectoryDoesNotExist($dir);

        DeleteDirectory::handle($dir);

        CreateDirectory::handle($dir);

        self::assertDirectoryExists($dir);

        DeleteDirectory::handle($dir);

        $topDir = \dirname($dir);

        self::assertDirectoryExists($topDir);

        DeleteDirectory::handle($topDir);

        self::assertDirectoryDoesNotExist($topDir);
    }

    public function testDeleteDirectory2(): void
    {
        $dir = __DIR__.'/deleteDirectory2/dir';

        $topDir = \dirname($dir);

        self::assertDirectoryDoesNotExist($dir);

        CreateDirectory::handle($dir);

        self::assertDirectoryExists($dir);

        DeleteDirectory::handle($topDir);

        self::assertDirectoryDoesNotExist($topDir);
    }

    #[Api([
        'zh-CN:title' => 'traverse_directory 遍历目录',
    ])]
    public function testTraverseDirectory(): void
    {
        $sourcePath = __DIR__.'/traverseDirectory';
        $sourceSubPath = __DIR__.'/traverseDirectory/dir';

        self::assertDirectoryDoesNotExist($sourceSubPath);

        CreateDirectory::handle($sourceSubPath);

        file_put_contents($testFile = $sourceSubPath.'/hello.txt', 'foo');

        self::assertTrue(is_file($testFile));

        self::assertSame('foo', file_get_contents($testFile));

        $filesAndDirs = [];
        $filesAndDirs2 = [];

        TraverseDirectory::handle($sourcePath, true, static function ($item) use (&$filesAndDirs): void {
            $filesAndDirs[] = $item->getFileName();
        });

        TraverseDirectory::handle($sourcePath, true, static function ($item) use (&$filesAndDirs2): void {
            $filesAndDirs2[] = $item->getFileName();
        }, ['hello.txt']);

        self::assertSame(['dir', 'hello.txt'], $filesAndDirs);
        self::assertSame(['dir'], $filesAndDirs2);

        DeleteDirectory::handle($sourcePath);
    }

    public function testTraverseDirectory2(): void
    {
        $sourcePath = __DIR__.'/traverseDirectory2';

        self::assertDirectoryDoesNotExist($sourcePath);

        TraverseDirectory::handle($sourcePath, true, static function ($item): void {});
    }

    #[Api([
        'zh-CN:title' => 'tidy_path 整理目录斜线风格',
    ])]
    public function testTidyPath(): void
    {
        $sourcePath = '/home\goods/name/';

        self::assertSame('/home/goods/name', TidyPath::handle($sourcePath));
        self::assertSame('\home\goods\name', TidyPath::handle($sourcePath, false));
    }

    #[Api([
        'zh-CN:title' => 'is_absolute_path 判断是否为绝对路径',
    ])]
    public function testIsAbsolutePath(): void
    {
        self::assertTrue(IsAbsolutePath::handle('c://'));
        self::assertTrue(IsAbsolutePath::handle('/path/hello'));
        self::assertFalse(IsAbsolutePath::handle('hello'));
    }

    #[Api([
        'zh-CN:title' => 'distributed 根据 ID 获取打散目录',
    ])]
    public function testDistributed(): void
    {
        self::assertSame(['000/00/00/', '01'], Distributed::handle(1));

        self::assertSame(['090/00/00/', '00'], Distributed::handle(90000000));
    }

    #[Api([
        'zh-CN:title' => 'create_file 创建文件',
    ])]
    public function testCreateFile(): void
    {
        $sourcePath = __DIR__.'/createFile';
        $file = $sourcePath.'/hello.txt';

        self::assertDirectoryDoesNotExist($sourcePath);

        CreateDirectory::handle($sourcePath);

        self::assertFalse(is_file($file));

        CreateFile::handle($file);

        self::assertTrue(is_file($file));

        DeleteDirectory::handle($sourcePath);
    }

    public function testCreateFile2(): void
    {
        $file = __DIR__.'/HelperTest.php';

        $this->expectException(IOException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to create "%s": mkdir(): File exists', $file)
        );

        CreateFile::handle($file.'/demo.txt');
    }

    public function testCreateFile3(): void
    {
        $sourcePath = __DIR__.'/createFile2';
        $file = $sourcePath.'/hello2.txt';

        $this->expectException(IOException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to touch "%s"', $file)
        );

        if (is_dir($sourcePath)) {
            rmdir($sourcePath);
        }

        self::assertDirectoryDoesNotExist($sourcePath);

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir($sourcePath, 0o444);

        self::assertFalse(is_file($file));

        CreateFile::handle($file);
    }

    public function testCreateFile4(): void
    {
        $sourcePath = __DIR__.'/foo/bar/createFile4';
        $file = $sourcePath.'/hello4.txt';

        self::assertDirectoryDoesNotExist($sourcePath);

        self::assertFalse(is_file($file));

        CreateFile::handle($file);

        self::assertTrue(is_file($file));

        CreateFile::handle($file);

        DeleteDirectory::handle($sourcePath);
    }

    public function testCreateFile5(): void
    {
        $sourcePath = __DIR__.'/foo/bar/createFile5';
        $file = $sourcePath.'/hello5.txt';

        self::assertDirectoryDoesNotExist($sourcePath);

        self::assertFalse(is_file($file));

        CreateFile::handle($file, 'hello');

        self::assertTrue(is_file($file));
        self::assertSame('hello', file_get_contents($file));

        CreateFile::handle($file, 'world', 0o666);
        self::assertSame('world', file_get_contents($file));

        DeleteDirectory::handle($sourcePath);
    }

    #[Api([
        'zh-CN:title' => 'extension 获取上传文件扩展名',
    ])]
    public function testExtension(): void
    {
        $file = __DIR__.'/HelperTest.pHp';

        self::assertSame('pHp', Extension::handle($file));
        self::assertSame('PHP', Extension::handle($file, 1));
        self::assertSame('php', Extension::handle($file, 2));
    }
}
