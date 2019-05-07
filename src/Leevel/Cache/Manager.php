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

namespace Leevel\Cache;

use Leevel\Cache\Redis\PhpRedis;
use Leevel\Manager\Manager as Managers;

/**
 * 缓存入口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.02.15
 *
 * @version 1.0
 */
class Manager extends Managers implements ICache
{
    /**
     * 批量插入.
     *
     * @param array|string $keys
     * @param mixed        $value
     */
    public function put($keys, $value = null): void
    {
        $this->connect()->put($keys, $value);
    }

    /**
     * 缓存存在读取否则重新设置.
     *
     * @param string $name
     * @param mixed  $data
     * @param array  $option
     *
     * @return mixed
     */
    public function remember(string $name, $data, array $option = [])
    {
        return $this->connect()->remember($name, $data, $option);
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value): ICache
    {
        return $this->connect()->setOption($name, $value);
    }

    /**
     * 获取缓存.
     *
     * @param string $name
     * @param mixed  $defaults
     * @param array  $option
     *
     * @return mixed
     */
    public function get(string $name, $defaults = false, array $option = [])
    {
        return $this->connect()->get($name, $defaults, $option);
    }

    /**
     * 设置缓存.
     *
     * @param string $name
     * @param mixed  $data
     * @param array  $option
     */
    public function set(string $name, $data, array $option = []): void
    {
        $this->connect()->set($name, $data, $option);
    }

    /**
     * 清除缓存.
     *
     * @param string $name
     */
    public function delete(string $name): void
    {
        $this->connect()->delete($name);
    }

    /**
     * 返回缓存句柄.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->connect()->handle();
    }

    /**
     * 关闭.
     */
    public function close()
    {
        $this->connect()->close();
    }

    /**
     * 取得配置命名空间.
     *
     * @return string
     */
    protected function normalizeOptionNamespace(): string
    {
        return 'cache';
    }

    /**
     * 创建连接对象
     *
     * @param object $connect
     *
     * @return object
     */
    protected function createConnect(object $connect): object
    {
        return $connect;
    }

    /**
     * 创建文件缓存.
     *
     * @param array $options
     *
     * @return \Leevel\Cache\File
     */
    protected function makeConnectFile(array $options = []): File
    {
        return new File(
            $this->normalizeConnectOption('file', $options)
        );
    }

    /**
     * 创建 redis 缓存.
     *
     * @param array $options
     *
     * @return \Leevel\Cache\Redis
     */
    protected function makeConnectRedis(array $options = []): Redis
    {
        $options = $this->normalizeConnectOption('redis', $options);

        return new Redis(new PhpRedis($options), $options);
    }

    /**
     * 分析连接配置.
     *
     * @param string $connect
     *
     * @return array
     */
    protected function getConnectOption(string $connect): array
    {
        return $this->filterNullOfOption(
            parent::getConnectOption($connect)
        );
    }
}
