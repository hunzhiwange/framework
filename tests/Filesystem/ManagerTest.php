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

use League\Flysystem\Filesystem as LeagueFilesystem;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
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
 * \App::make('filesystems')->put(string $path, string $contents, array $config = []): bool;
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
 * \Leevel\Filesystem\Proxy\Filesystem::put(string $path, string $contents, array $config = []): bool;
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
 */
class ManagerTest extends TestCase
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

        $manager->put('hellomanager.txt', 'manager');
        $file = $path.'/hellomanager.txt';

        $this->assertTrue(is_file($file));
        $this->assertSame('manager', file_get_contents($file));

        unlink($file);
        rmdir($path);
    }

    protected function createManager(): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'filesystem' => [
                'default' => 'local',
                'connect' => [
                    'local' => [
                        'driver'  => 'local',
                        'path'    => __DIR__.'/forManager',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        return $manager;
    }
}
