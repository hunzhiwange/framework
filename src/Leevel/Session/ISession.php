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

namespace Leevel\Session;

use Leevel\Cache\ICache;

/**
 * ISession 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.11
 *
 * @version 1.0
 *
 * @see \SessionHandlerInterface 底层接口参考
 */
interface ISession
{
    /**
     * 默认 session 名字.
     *
     * @var string
     */
    const SESSION_NAME = 'UID';

    /**
     * 启动 session.
     *
     * @param string $sessionId
     */
    public function start(?string $sessionId = null): void;

    /**
     * 程序执行保存 session.
     */
    public function save(): void;

    /**
     * 取回所有 session 数据.
     *
     * @return array
     */
    public function all(): array;

    /**
     * 设置 session.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set(string $name, $value): void;

    /**
     * 批量插入.
     *
     * @param array|string $keys
     * @param mixed        $value
     */
    public function put($keys, $value = null): void;

    /**
     * 数组插入数据.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function push(string $key, $value): void;

    /**
     * 合并元素.
     *
     * @param string $key
     * @param array  $value
     */
    public function merge(string $key, array $value): void;

    /**
     * 弹出元素.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function pop(string $key, array $value): void;

    /**
     * 数组插入键值对数据.
     *
     * @param string $key
     * @param mixed  $keys
     * @param mixed  $value
     */
    public function arr(string $key, $keys, $value = null): void;

    /**
     * 数组键值删除数据.
     *
     * @param string $key
     * @param mixed  $keys
     */
    public function arrDelete(string $key, $keys): void;

    /**
     * 取回 session.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function get(string $name, $value = null);

    /**
     * 返回数组部分数据.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function getPart(string $name, $value = null);

    /**
     * 删除 session.
     *
     * @param string $name
     */
    public function delete(string $name): void;

    /**
     * 是否存在 session.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * 删除 session.
     */
    public function clear(): void;

    /**
     * 闪存一个数据，当前请求和下一个请求可用.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function flash(string $key, $value): void;

    /**
     * 批量闪存数据，当前请求和下一个请求可用.
     *
     * @param array $flash
     */
    public function flashs(array $flash): void;

    /**
     * 闪存一个 flash 用于当前请求使用，下一个请求将无法获取.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function nowFlash(string $key, $value): void;

    /**
     * 保持所有闪存数据.
     */
    public function rebuildFlash(): void;

    /**
     * 保持闪存数据.
     *
     * @param array $keys
     */
    public function keepFlash(array $keys): void;

    /**
     * 返回闪存数据.
     *
     * @param string $key
     * @param mixed  $defaults
     *
     * @return mixed
     */
    public function getFlash(string $key, $defaults = null);

    /**
     * 删除闪存数据.
     *
     * @param array $keys
     */
    public function deleteFlash(array $keys): void;

    /**
     * 清理所有闪存数据.
     */
    public function clearFlash(): void;

    /**
     * 程序执行结束清理 flash.
     */
    public function unregisterFlash(): void;

    /**
     * 获取前一个请求地址
     *
     * @return null|string
     */
    public function prevUrl(): ?string;

    /**
     * 设置前一个请求地址
     *
     * @param string $url
     */
    public function setPrevUrl(string $url): void;

    /**
     * 终止会话.
     */
    public function destroySession(): void;

    /**
     * session 是否已经启动.
     *
     * @return bool
     */
    public function isStart(): bool;

    /**
     * 设置 SESSION 名字.
     *
     * @param string $name
     */
    public function setName(string $name): void;

    /**
     * 取得 SESSION 名字.
     *
     * @return string
     */
    public function getName(): ?string;

    /**
     * 设置 SESSION ID.
     *
     * @param string $id
     */
    public function setId(?string $id = null): void;

    /**
     * 取得 SESSION ID.
     *
     * @return string
     */
    public function getId(): ?string;

    /**
     * 重新生成 SESSION ID.
     */
    public function regenerateId(): string;

    /**
     * 返回缓存仓储.
     *
     * @return \Leevel\Cache\ICache
     */
    public function getCache(): ?ICache;

    /**
     * open.
     *
     * @param string $savePath
     * @param string $sessionName
     *
     * @return bool
     */
    public function open(string $savePath, string $sessionName): bool;

    /**
     * close.
     *
     * @return bool
     */
    public function close(): bool;

    /**
     * read.
     *
     * @param string $sessionId
     *
     * @return string
     */
    public function read(string $sessionId): string;

    /**
     * write.
     *
     * @param string $sessionId
     * @param string $sessionData
     *
     * @return bool
     */
    public function write(string $sessionId, string $sessionData): bool;

    /**
     * destroy.
     *
     * @param string $sessionId
     *
     * @return bool
     */
    public function destroy(string $sessionId): bool;

    /**
     * gc.
     *
     * @param int $maxLifetime
     *
     * @return int
     */
    public function gc(int $maxLifetime): int;
}
