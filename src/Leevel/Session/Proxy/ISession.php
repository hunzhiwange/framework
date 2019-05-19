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

namespace Leevel\Session\Proxy;

/**
 * 代理 session 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.11
 *
 * @version 1.0
 *
 * @see \Leevel\Session\ISession 请保持接口设计的一致性
 */
interface ISession
{
    /**
     * 启动 session.
     *
     * @param string $sessionId
     */
    public static function start(?string $sessionId = null): void;

    /**
     * 程序执行保存 session.
     */
    public static function save(): void;

    /**
     * 取回所有 session 数据.
     *
     * @return array
     */
    public static function all(): array;

    /**
     * 设置 session.
     *
     * @param string $name
     * @param mixed  $value
     */
    public static function set(string $name, $value): void;

    /**
     * 批量插入.
     *
     * @param array|string $keys
     * @param mixed        $value
     */
    public static function put($keys, $value = null): void;

    /**
     * 数组插入数据.
     *
     * @param string $key
     * @param mixed  $value
     */
    public static function push(string $key, $value): void;

    /**
     * 合并元素.
     *
     * @param string $key
     * @param array  $value
     */
    public static function merge(string $key, array $value): void;

    /**
     * 弹出元素.
     *
     * @param string $key
     * @param mixed  $value
     */
    public static function pop(string $key, array $value): void;

    /**
     * 数组插入键值对数据.
     *
     * @param string $key
     * @param mixed  $keys
     * @param mixed  $value
     */
    public static function arr(string $key, $keys, $value = null): void;

    /**
     * 数组键值删除数据.
     *
     * @param string $key
     * @param mixed  $keys
     */
    public static function arrDelete(string $key, $keys): void;

    /**
     * 取回 session.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public static function get(string $name, $value = null);

    /**
     * 返回数组部分数据.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public static function getPart(string $name, $value = null);

    /**
     * 删除 session.
     *
     * @param string $name
     */
    public static function delete(string $name): void;

    /**
     * 是否存在 session.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function has(string $name): bool;

    /**
     * 删除 session.
     */
    public static function clear(): void;

    /**
     * 闪存一个数据，当前请求和下一个请求可用.
     *
     * @param string $key
     * @param mixed  $value
     */
    public static function flash(string $key, $value): void;

    /**
     * 批量闪存数据，当前请求和下一个请求可用.
     *
     * @param array $flash
     */
    public static function flashs(array $flash): void;

    /**
     * 闪存一个 flash 用于当前请求使用，下一个请求将无法获取.
     *
     * @param string $key
     * @param mixed  $value
     */
    public static function nowFlash(string $key, $value): void;

    /**
     * 保持所有闪存数据.
     */
    public static function rebuildFlash(): void;

    /**
     * 保持闪存数据.
     *
     * @param array $keys
     */
    public static function keepFlash(array $keys): void;

    /**
     * 返回闪存数据.
     *
     * @param string $key
     * @param mixed  $defaults
     *
     * @return mixed
     */
    public static function getFlash(string $key, $defaults = null);

    /**
     * 删除闪存数据.
     *
     * @param array $keys
     */
    public static function deleteFlash(array $keys): void;

    /**
     * 清理所有闪存数据.
     */
    public static function clearFlash(): void;

    /**
     * 程序执行结束清理 flash.
     */
    public static function unregisterFlash(): void;

    /**
     * 获取前一个请求地址
     *
     * @return null|string
     */
    public static function prevUrl(): ?string;

    /**
     * 设置前一个请求地址
     *
     * @param string $url
     */
    public static function setPrevUrl(string $url): void;

    /**
     * 终止会话.
     */
    public static function destroySession(): void;

    /**
     * session 是否已经启动.
     *
     * @return bool
     */
    public static function isStart(): bool;

    /**
     * 设置 SESSION 名字.
     *
     * @param string $name
     */
    public static function setName(string $name): void;

    /**
     * 取得 SESSION 名字.
     *
     * @return string
     */
    public static function getName(): ?string;

    /**
     * 设置 SESSION ID.
     *
     * @param string $id
     */
    public static function setId(?string $id = null): void;

    /**
     * 取得 SESSION ID.
     *
     * @return string
     */
    public static function getId(): ?string;

    /**
     * 重新生成 SESSION ID.
     */
    public static function regenerateId(): string;

    /**
     * open.
     *
     * @param string $savePath
     * @param string $sessionName
     *
     * @return bool
     */
    public static function open(string $savePath, string $sessionName): bool;

    /**
     * close.
     *
     * @return bool
     */
    public static function close(): bool;

    /**
     * read.
     *
     * @param string $sessionId
     *
     * @return string
     */
    public static function read(string $sessionId): string;

    /**
     * write.
     *
     * @param string $sessionId
     * @param string $sessionData
     *
     * @return bool
     */
    public static function write(string $sessionId, string $sessionData): bool;

    /**
     * destroy.
     *
     * @param string $sessionId
     *
     * @return bool
     */
    public static function destroy(string $sessionId): bool;

    /**
     * gc.
     *
     * @param int $maxLifetime
     *
     * @return int
     */
    public static function gc(int $maxLifetime): int;
}
