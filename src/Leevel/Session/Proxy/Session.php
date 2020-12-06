<?php

declare(strict_types=1);

namespace Leevel\Session\Proxy;

use Leevel\Di\Container;
use Leevel\Session\Manager;

/**
 * 代理 session.
 *
 * @method static void start(?string $sessionId = null)              启动 Session.
 * @method static void save()                                        程序执行保存 Session.
 * @method static void setExpire(?int $expire = null)                设置过期时间.
 * @method static array all()                                        取回所有 Session 数据.
 * @method static void set(string $name, $value)                     设置 Session.
 * @method static void put($keys, $value = null)                     批量插入.
 * @method static mixed get(string $name, $value = null)             取回 Session.
 * @method static void delete(string $name)                          删除 Session.
 * @method static bool has(string $name)                             是否存在 Session.
 * @method static void clear()                                       删除 Session.
 * @method static void flash(string $key, $value)                    闪存一个数据，当前请求和下一个请求可用.
 * @method static void flashs(array $flash)                          批量闪存数据，当前请求和下一个请求可用.
 * @method static void nowFlash(string $key, $value)                 闪存数据用于当前请求使用，下一个请求将无法获取.
 * @method static void rebuildFlash()                                保持所有闪存数据.
 * @method static void keepFlash(array $keys)                        保持闪存数据.
 * @method static mixed getFlash(string $key, $defaults = null)      返回闪存数据.
 * @method static void deleteFlash(array $keys)                      删除闪存数据.
 * @method static void clearFlash()                                  清理所有闪存数据.
 * @method static void unregisterFlash()                             程序执行结束清理闪存数据.
 * @method static string prevUrl()                                   获取前一个请求地址.
 * @method static void setPrevUrl(string $url)                       设置前一个请求地址.
 * @method static void destroySession()                              终止会话.
 * @method static bool isStart()                                     Session 是否已经启动.
 * @method static void setName(string $name)                         设置 Session 名字.
 * @method static string getName()                                   取得 Session 名字.
 * @method static void setId(?string $id = null)                     设置 Session ID.
 * @method static string getId()                                     取得 Session ID.
 * @method static string regenerateId()                              重新生成 Session ID.
 * @method static bool open(string $savePath, string $sessionName)   Open.
 * @method static bool close()                                       Close.
 * @method static string read(string $sessionId)                     Read.
 * @method static bool write(string $sessionId, string $sessionData) Write.
 * @method static bool destroy(string $sessionId)                    Destroy.
 * @method static int gc(int $maxLifetime)                           Gc.
 */
class Session
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        return Container::singletons()->make('sessions');
    }
}
