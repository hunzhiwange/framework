<?php

declare(strict_types=1);

namespace Leevel\Cache;

use Leevel\Manager\Manager as Managers;

/**
 * 缓存管理器.
 *
 * @method static void put($keys, $value = null, ?int $expire = null)                             批量插入.
 * @method static mixed remember(string $name, \Closure $dataGenerator, ?int $expire = null)      缓存存在读取否则重新设置.
 * @method static mixed get(string $name, $defaults = false)                                      获取缓存.
 * @method static void set(string $name, $data, ?int $expire = null)                              设置缓存.
 * @method static void delete(string $name)                                                       清除缓存.
 * @method static bool has(string $name)                                                          缓存是否存在.
 * @method static mixed increase(string $name, int $step = 1, ?int $expire = null)                自增.
 * @method static mixed decrease(string $name, int $step = 1, ?int $expire = null)                自减.
 * @method static int ttl(string $name)                                                           获取缓存剩余时间.
 * @method static mixed handle()                                                                  返回缓存句柄.
 * @method static void close()                                                                    关闭.
 * @method static void setKeyRegex(string $keyRegex)                                              设置缓存键值正则.
 * @method static \Leevel\Di\IContainer container()                                               返回 IOC 容器.
 * @method static void disconnect(?string $connect = null)                                        删除连接.
 * @method static array getConnects()                                                             取回所有连接.
 * @method static string getDefaultConnect()                                                      返回默认连接.
 * @method static void setDefaultConnect(string $name)                                            设置默认连接.
 * @method static mixed getContainerOption(?string $name = null)                                  获取容器配置值.
 * @method static void setContainerOption(string $name, mixed $value)                             设置容器配置值.
 * @method static void extend(string $connect, \Closure $callback)                                扩展自定义连接.
 * @method static array normalizeConnectOption(string $connect)                                   整理连接配置.
 */
class Manager extends Managers
{
    /**
     * {@inheritDoc}
     */
    public function connect(?string $connect = null, bool $newConnect = false): ICache
    {
        return parent::connect($connect, $newConnect);
    }

    /**
     * {@inheritDoc}
     */
    public function reconnect(?string $connect = null): ICache
    {
        return parent::reconnect($connect);
    }

    /**
     * 取得配置命名空间.
     */
    protected function getOptionNamespace(): string
    {
        return 'cache';
    }

    /**
     * 创建文件缓存.
     */
    protected function makeConnectFile(string $connect): File
    {
        return new File(
            $this->normalizeConnectOption($connect)
        );
    }

    /**
     * 创建 redis 缓存.
     */
    protected function makeConnectRedis(string $connect, ?string $driver = null): Redis
    {
        $options = $this->normalizeConnectOption($connect);
        $driver = $driver ?? Redis::class;

        return new $driver($this->container->make('redis'), $options);
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
