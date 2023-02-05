<?php

declare(strict_types=1);

namespace Leevel\Session;

use Leevel\Support\Manager as Managers;

/**
 * Session 管理器.
 *
 * @method static void                  start(?string $sessionId = null)               启动 session.
 * @method static void                  save()                                         程序执行保存 session.
 * @method static void                  setExpire(?int $expire = null)                 设置过期时间.
 * @method static array                 all()                                          取回所有 session 数据.
 * @method static void                  set(string $name, $value)                      设置 session.
 * @method static void                  put($keys, $value = null)                      批量插入.
 * @method static mixed                 get(string $name, $value = null)               取回 session.
 * @method static void                  delete(string $name)                           删除 session.
 * @method static bool                  has(string $name)                              是否存在 session.
 * @method static void                  clear()                                        删除 session.
 * @method static void                  flash(string $key, $value)                     闪存一个数据，当前请求和下一个请求可用.
 * @method static void                  flashs(array $flash)                           批量闪存数据，当前请求和下一个请求可用.
 * @method static void                  nowFlash(string $key, $value)                  闪存一个 flash 用于当前请求使用，下一个请求将无法获取.
 * @method static void                  rebuildFlash()                                 保持所有闪存数据.
 * @method static void                  keepFlash(array $keys)                         保持闪存数据.
 * @method static mixed                 getFlash(string $key, $defaults = null)        返回闪存数据.
 * @method static void                  deleteFlash(array $keys)                       删除闪存数据.
 * @method static void                  clearFlash()                                   清理所有闪存数据.
 * @method static void                  unregisterFlash()                              程序执行结束清理 flash.
 * @method static string                prevUrl()                                      获取前一个请求地址.
 * @method static void                  setPrevUrl(string $url)                        设置前一个请求地址.
 * @method static void                  destroySession()                               终止会话.
 * @method static bool                  isStart()                                      session 是否已经启动.
 * @method static void                  setName(string $name)                          设置 SESSION 名字.
 * @method static string                getName()                                      取得 SESSION 名字.
 * @method static void                  setId(?string $id = null)                      设置 SESSION ID.
 * @method static string                getId()                                        取得 SESSION ID.
 * @method static string                regenerateId()                                 重新生成 SESSION ID.
 * @method static bool                  open(string $savePath, string $sessionName)    open.
 * @method static bool                  close()                                        close.
 * @method static string                read(string $sessionId)                        read.
 * @method static bool                  write(string $sessionId, string $sessionData)  write.
 * @method static bool                  destroy(string $sessionId)                     destroy.
 * @method static int                   gc(int $maxLifetime)                           gc.
 * @method static \Leevel\Di\IContainer container()                                    返回 IOC 容器.
 * @method static void                  disconnect(?string $connect = null)            删除连接.
 * @method static array                 getConnects()                                  取回所有连接.
 * @method static string                getDefaultConnect()                            返回默认连接.
 * @method static void                  setDefaultConnect(string $name)                设置默认连接.
 * @method static mixed                 getContainerOption(?string $name = null)       获取容器配置值.
 * @method static void                  setContainerOption(string $name, mixed $value) 设置容器配置值.
 * @method static void                  extend(string $connect, \Closure $callback)    扩展自定义连接.
 * @method static array                 normalizeConnectOption(string $connect)        整理连接配置.
 */
class Manager extends Managers
{
    /**
     * {@inheritDoc}
     */
    public function connect(?string $connect = null, bool $newConnect = false): ISession
    {
        return parent::connect($connect, $newConnect);
    }

    /**
     * {@inheritDoc}
     */
    public function reconnect(?string $connect = null): ISession
    {
        return parent::reconnect($connect);
    }

    /**
     * 返回 session 配置.
     */
    public function getSessionOption(): array
    {
        return $this->normalizeConnectOption($this->getDefaultConnect());
    }

    /**
     * 取得配置命名空间.
     */
    protected function getOptionNamespace(): string
    {
        return 'session';
    }

    /**
     * 创建 test 缓存.
     */
    protected function makeConnectTest(string $connect, ?string $driverClass = null): Test
    {
        $driverClass = $this->getDriverClass(Test::class, $driverClass);

        return new $driverClass(
            $this->normalizeConnectOption($connect)
        );
    }

    /**
     * 创建 file 缓存.
     */
    protected function makeConnectFile(string $connect, ?string $driverClass = null): File
    {
        $driverClass = $this->getDriverClass(File::class, $driverClass);
        $options = $this->normalizeConnectOption($connect);

        /** @var \Leevel\Cache\File $file */
        $file = $this->container['caches']->connect($options['file_driver']);

        return new $driverClass($file, $options);
    }

    /**
     * 创建 redis 缓存.
     */
    protected function makeConnectRedis(string $connect, ?string $driverClass = null): Redis
    {
        $driverClass = $this->getDriverClass(Redis::class, $driverClass);
        $options = $this->normalizeConnectOption($connect);

        /** @var \Leevel\Cache\Redis $redis */
        $redis = $this->container['caches']->connect($options['redis_driver']);

        return new $driverClass($redis, $options);
    }

    /**
     * 分析连接配置.
     */
    protected function getConnectOption(string $connect): array
    {
        return $this->filterNullOfOption(parent::getConnectOption($connect));
    }
}
