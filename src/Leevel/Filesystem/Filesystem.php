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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Filesystem;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem as LeagueFilesystem;

/**
 * Filesystem 驱动抽象类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.29
 * @see https://flysystem.thephpleague.com/api/
 *
 * @version 1.0
 */
abstract class Filesystem
{
    use Proxy;

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
     *
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);

        $this->filesystem();
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return $this->filesystem->{$method}(...$args);
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value): IFilesystem
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 返回 Filesystem.
     *
     * @return \League\Flysystem\Filesystem
     */
    public function getFilesystem(): LeagueFilesystem
    {
        return $this->filesystem;
    }

    /**
     * 返回代理.
     *
     * @return \League\Flysystem\Filesystem
     */
    public function proxy(): LeagueFilesystem
    {
        return $this->filesystem;
    }

    /**
     * 创建连接.
     *
     * @return \League\Flysystem\AdapterInterface
     */
    abstract protected function makeAdapter(): AdapterInterface;

    /**
     * 生成 Filesystem.
     *
     * @return \League\Flysystem\Filesystem
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
     *
     * @param array $option
     *
     * @return array
     */
    protected function normalizeOptions(array $option = []): array
    {
        return $option ? array_merge($this->option, $option) : $this->option;
    }
}
