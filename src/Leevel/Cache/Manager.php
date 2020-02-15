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

namespace Leevel\Cache;

use Leevel\Manager\Manager as Managers;
use RuntimeException;

/**
 * 缓存管理器.
 *
 * @method static void put($keys, $value = null)                           批量插入.
 * @method static remember(string $name, $data, array $option = [])        缓存存在读取否则重新设置.
 * @method static \Leevel\Cache\ICache setOption(string $name, $value)     设置配置.
 * @method static get(string $name, $defaults = false, array $option = []) 获取缓存.
 * @method static void set(string $name, $data, array $option = [])        设置缓存.
 * @method static void delete(string $name)                                清除缓存.
 * @method static handle()                                                 返回缓存句柄.
 * @method static void close()                                             关闭.
 */
class Manager extends Managers
{
    /**
     * 取得配置命名空间.
     */
    protected function normalizeOptionNamespace(): string
    {
        return 'cache';
    }

    /**
     * 创建文件缓存.
     *
     * @return \Leevel\Cache\File
     */
    protected function makeConnectFile(): File
    {
        return new File(
            $this->normalizeConnectOption('file')
        );
    }

    /**
     * 创建 redis 缓存.
     *
     * @return \Leevel\Cache\Redis
     */
    protected function makeConnectRedis(): Redis
    {
        $options = $this->normalizeConnectOption('redis');
        $phpRedis = $this->container->make('redis');

        return new Redis($phpRedis, $options);
    }

    /**
     * 创建 redisPool 缓存.
     *
     * @return \Leevel\Cache\RedisPool
     */
    protected function makeConnectRedisPool(): RedisPool
    {
        if (!$this->container->getCoroutine()) {
            $e = 'Redis pool can only be used in swoole scenarios.';

            throw new RuntimeException($e);
        }

        $redisPool = $this->container->make('redis.pool');

        return new RedisPool($redisPool);
    }

    /**
     * 分析连接配置.
     */
    protected function getConnectOption(string $connect): array
    {
        return $this->filterNullOfOption(
            parent::getConnectOption($connect)
        );
    }
}
