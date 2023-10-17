<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Filesystem\Manager;
use Leevel\Option\Option;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Filesystem",
 *     path="component/filesystem",
 *     zh-CN:description="
 * 文件管理统一由文件组件完成，通常我们使用代理 `\Leevel\Filesystem\Proxy\Filesystem` 类进行静态调用。
 *
 * 内置支持的 filesystem 驱动类型包括 local、zip、ftp、sftp，未来可能增加其他驱动。
 *
 * ::: tip
 * 文件系统底层基于 league/flysystem 开发，相关文档可以参考 <https://flysystem.thephpleague.com/docs/>。
 * :::
 *
 * ## 使用方式
 *
 * 使用容器 flysystems 服务
 *
 * ``` php
 * \App::make('filesystems')->write(string $path, string $contents, array $config = []): bool;
 * ```
 *
 * 依赖注入
 *
 * ``` php
 * class Demo
 * {
 *     private \Leevel\Filesystem\Manager $filesystem;
 *
 *     public function __construct(\Leevel\Filesystem\Manager $filesystem)
 *     {
 *         $this->filesystem = $filesystem;
 *     }
 * }
 * ```
 *
 * 使用静态代理
 *
 * ``` php
 * \Leevel\Filesystem\Proxy\Filesystem::write(string $path, string $contents, array $config = []): bool;
 * ```
 *
 * ## filesystem 配置
 *
 * 系统的 filesystem 配置位于应用下面的 `option/filesystem.php` 文件。
 *
 * 可以定义多个文件系统连接，并且支持切换，每一个连接支持驱动设置。
 *
 * ``` php
 * {[file_get_contents('option/filesystem.php')]}
 * ```
 *
 * filesystem 参数根据不同的连接会有所区别。
 * ",
 * )
 *
 * @internal
 */
final class ManagerTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="文件系统基本使用方法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $manager = $this->createManager();
        $path = __DIR__.'/forManager';
        $this->assertInstanceof(LeagueFilesystem::class, $manager->getFilesystem());

        $manager->write('hellomanager.txt', 'manager');
        $file = $path.'/hellomanager.txt';

        static::assertTrue(is_file($file));
        static::assertSame('manager', file_get_contents($file));
        $this->clearTempDir();
    }

    public function test1(): void
    {
        $manager = $this->createManager();
        $path = __DIR__.'/forManager';
        $this->assertInstanceof(LeagueFilesystem::class, $manager->getFilesystem());

        $connect = $manager->reconnect();
        $connect->write('hellomanager.txt', 'manager');
        $file = $path.'/hellomanager.txt';

        static::assertTrue(is_file($file));
        static::assertSame('manager', file_get_contents($file));
        $this->clearTempDir();
    }

    public function testZip(): void
    {
        $manager = $this->createManager('zip');
        $this->assertInstanceof(LeagueFilesystem::class, $manager->getFilesystem());
    }

    public function testFtp(): void
    {
        $manager = $this->createManager('ftp');
        $this->assertInstanceof(LeagueFilesystem::class, $manager->getFilesystem());
    }

    public function testSftp(): void
    {
        $manager = $this->createManager('sftp');
        $this->assertInstanceof(LeagueFilesystem::class, $manager->getFilesystem());
    }

    protected function createManager(string $connect = 'local'): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'filesystem' => [
                'default' => $connect,
                'connect' => [
                    'local' => [
                        'driver' => 'local',
                        'path' => __DIR__.'/forManager',
                    ],
                    'zip' => [
                        'driver' => 'zip',
                        'path' => __DIR__.'/forManager2/filesystem.zip',
                    ],
                    'ftp' => [
                        'driver' => 'ftp',
                        'host' => 'ftp.example.com',
                        'port' => 21,
                        'username' => 'your-username',
                        'password' => 'your-password',
                        'root' => '',
                        'passive' => true,
                        'ssl' => false,
                        'timeout' => 20,
                    ],
                    'sftp' => [
                        'driver' => 'sftp',
                        'host' => 'sftp.example.com',
                        'port' => 22,
                        'username' => 'your-username',
                        'password' => 'your-password',
                        'root' => '',
                        'privateKey' => '',
                        'timeout' => 20,
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        return $manager;
    }

    protected function clearTempDir(): void
    {
        $dirs = [
            __DIR__.'/forManager',
        ];
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                Helper::deleteDirectory($dir);
            }
        }
    }
}
