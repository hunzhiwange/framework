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

namespace Leevel\Session\Proxy;

use Leevel\Di\Container;
use Leevel\Session\Manager;

/**
 * 代理 session.
 *
 * @method static void start(?string $sessionId = null)              启动 session.
 * @method static void save()                                        程序执行保存 session.
 * @method static void setExpire(?int $expire = null)                设置过期时间.
 * @method static array all()                                        取回所有 session 数据.
 * @method static void set(string $name, $value)                     设置 session.
 * @method static void put($keys, $value = null)                     批量插入.
 * @method static mixed get(string $name, $value = null)             取回 session.
 * @method static void delete(string $name)                          删除 session.
 * @method static bool has(string $name)                             是否存在 session.
 * @method static void clear()                                       删除 session.
 * @method static void flash(string $key, $value)                    闪存一个数据，当前请求和下一个请求可用.
 * @method static void flashs(array $flash)                          批量闪存数据，当前请求和下一个请求可用.
 * @method static void nowFlash(string $key, $value)                 闪存一个 flash 用于当前请求使用，下一个请求将无法获取.
 * @method static void rebuildFlash()                                保持所有闪存数据.
 * @method static void keepFlash(array $keys)                        保持闪存数据.
 * @method static mixed getFlash(string $key, $defaults = null)      返回闪存数据.
 * @method static void deleteFlash(array $keys)                      删除闪存数据.
 * @method static void clearFlash()                                  清理所有闪存数据.
 * @method static void unregisterFlash()                             程序执行结束清理 flash.
 * @method static string prevUrl()                                   获取前一个请求地址.
 * @method static void setPrevUrl(string $url)                       设置前一个请求地址.
 * @method static void destroySession()                              终止会话.
 * @method static bool isStart()                                     session 是否已经启动.
 * @method static void setName(string $name)                         设置 SESSION 名字.
 * @method static string getName()                                   取得 SESSION 名字.
 * @method static void setId(?string $id = null)                     设置 SESSION ID.
 * @method static string getId()                                     取得 SESSION ID.
 * @method static string regenerateId()                              重新生成 SESSION ID.
 * @method static bool open(string $savePath, string $sessionName)   open.
 * @method static bool close()                                       close.
 * @method static string read(string $sessionId)                     read.
 * @method static bool write(string $sessionId, string $sessionData) write.
 * @method static bool destroy(string $sessionId)                    destroy.
 * @method static int gc(int $maxLifetime)                           gc.
 */
class Session
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
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
