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

namespace Leevel\Filesystem;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem as LeagueFilesystem;

/**
 * 文件系统抽象类.
 *
 * @see https://flysystem.thephpleague.com/api/
 *
 * @method static bool has(string $path)                                                                     判断文件是否存在.
 * @method static mixed read(string $path)                                                                   读取文件.
 * @method static mixed readStream(string $path)                                                             从路径读取流数据.
 * @method static array listContents(string $directory = '', bool $recursive = false)                        读取文件目录.
 * @method static mixed getMetadata(string $path)                                                            获取文件元数据.
 * @method static mixed getSize(string $path)                                                                获取文件大小.
 * @method static mixed getMimetype(string $path)                                                            获取文件的 mime 类型.
 * @method static mixed getTimestamp(string $path)                                                           获取文件的时间戳.
 * @method static mixed getVisibility(string $path)                                                          获取文件的可见性.
 * @method static bool write(string $path, string $contents, array $config = [])                             写一个新文件.
 * @method static bool writeStream(string $path, $resource, array $config = [])                              使用流写入新文件.
 * @method static bool update(string $path, string $contents, array $config = [])                            更新现有文件.
 * @method static bool updateStream(string $path, $resource, array $config = [])                             使用流更新现有文件.
 * @method static bool rename(string $path, string $newpath)                                                 重命名文件.
 * @method static bool copy(string $path, string $newpath)                                                   复制文件.
 * @method static bool delete(string $path)                                                                  删除文件.
 * @method static bool deleteDir(string $dirname)                                                            删除文件夹.
 * @method static bool createDir(string $dirname, array $config = [])                                        创建一个文件夹.
 * @method static bool setVisibility(string $path, $visibility)                                              设置文件的可见性.
 * @method static bool put(string $path, string $contents, array $config = [])                               创建或者更新文件.
 * @method static bool putStream(string $path, $resource, array $config = [])                                使用流创建或者更新文件.
 * @method static mixed readAndDelete(string $path)                                                          读取并删除一个文件.
 * @method static \League\Flysystem\FilesystemInterface addPlugin(\League\Flysystem\PluginInterface $plugin) 注册一个插件.
 */
abstract class Filesystem
{
    /**
     * Filesystem.
     *
     * @var \League\Flysystem\Filesystem
     */
    protected LeagueFilesystem $filesystem;

    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [];

    /**
     * 构造函数.
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
        $this->filesystem();
    }

    /**
     * call.
     *
     * @return mixed
     * @codeCoverageIgnore
     */
    public function __call(string $method, array $args)
    {
        return $this->filesystem->{$method}(...$args);
    }

    /**
     * 设置配置.
     *
     * @param mixed $value
     *
     * @return \Leevel\Filesystem\IFilesystem
     */
    public function setOption(string $name, $value): IFilesystem
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 返回 Filesystem.
     */
    public function getFilesystem(): LeagueFilesystem
    {
        return $this->filesystem;
    }

    /**
     * 创建连接.
     */
    abstract protected function makeAdapter(): AdapterInterface;

    /**
     * 生成 Filesystem.
     */
    protected function filesystem(): LeagueFilesystem
    {
        return $this->filesystem = new LeagueFilesystem(
            $this->makeAdapter(),
            $this->normalizeOptions()
        );
    }

    /**
     * 整理配置.
     */
    protected function normalizeOptions(array $option = []): array
    {
        return $option ? array_merge($this->option, $option) : $this->option;
    }
}
