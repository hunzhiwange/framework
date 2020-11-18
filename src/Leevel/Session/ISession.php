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

namespace Leevel\Session;

/**
 * Session 接口.
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
     * 闪存值键前缀.
     *
     * @var string
     */
    const FLASH_DATA_KEY_PREFIX = 'flash.data.';

    /**
     * 新值闪存键.
     *
     * @var string
     */
    const FLASH_NEW_KEY = 'flash.new.key';

    /**
     * 旧值闪存键.
     *
     * @var string
     */
    const FLASH_OLD_KEY = 'flash.old.key';

    /**
     * 前一个页面键.
     *
     * @var string
     */
    const PREV_URL_KEY = 'prev.url.key';

    /**
     * 启动 session.
     */
    public function start(?string $sessionId = null): void;

    /**
     * 程序执行保存 session.
     */
    public function save(): void;

    /**
     * 设置过期时间.
     */
    public function setExpire(?int $expire = null): void;

    /**
     * 取回所有 session 数据.
     */
    public function all(): array;

    /**
     * 设置 session.
     *
     * @param mixed $value
     */
    public function set(string $name, mixed $value): void;

    /**
     * 批量插入.
     */
    public function put(array|string $keys, mixed $value = null): void;

    /**
     * 取回 session.
     *
     * @param mixed $defaults
     *
     * @return mixed
     */
    public function get(string $name, mixed $defaults = null): mixed;

    /**
     * 删除 session.
     */
    public function delete(string $name): void;

    /**
     * 是否存在 session.
     */
    public function has(string $name): bool;

    /**
     * 清空 session.
     */
    public function clear(): void;

    /**
     * 闪存一个数据，当前请求和下一个请求可用.
     *
     * @param mixed $value
     */
    public function flash(string $key, mixed $value): void;

    /**
     * 批量闪存数据，当前请求和下一个请求可用.
     */
    public function flashs(array $flash): void;

    /**
     * 闪存一个 flash 用于当前请求使用，下一个请求将无法获取.
     *
     * @param mixed $value
     */
    public function nowFlash(string $key, mixed $value): void;

    /**
     * 保持所有闪存数据.
     */
    public function rebuildFlash(): void;

    /**
     * 保持闪存数据.
     */
    public function keepFlash(array $keys): void;

    /**
     * 返回闪存数据.
     *
     * @param mixed $defaults
     *
     * @return mixed
     */
    public function getFlash(string $key, mixed $defaults = null): mixed;

    /**
     * 删除闪存数据.
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
     * 获取前一个请求地址.
     */
    public function prevUrl(): ?string;

    /**
     * 设置前一个请求地址.
     */
    public function setPrevUrl(string $url): void;

    /**
     * 终止会话.
     */
    public function destroySession(): void;

    /**
     * session 是否已经启动.
     */
    public function isStart(): bool;

    /**
     * 设置 SESSION 名字.
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
     */
    public function setId(?string $id = null): void;

    /**
     * 取得 SESSION ID.
     */
    public function getId(): string;

    /**
     * 重新生成 SESSION ID.
     */
    public function regenerateId(): string;

    /**
     * open.
     */
    public function open(string $savePath, string $sessionName): bool;

    /**
     * close.
     */
    public function close(): bool;

    /**
     * read.
     */
    public function read(string $sessionId): string;

    /**
     * write.
     */
    public function write(string $sessionId, string $sessionData): bool;

    /**
     * destroy.
     */
    public function destroy(string $sessionId): bool;

    /**
     * gc.
     */
    public function gc(int $maxLifetime): int;
}
