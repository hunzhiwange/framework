<?php

declare(strict_types=1);

namespace Leevel\Session;

/**
 * Session 接口.
 *
 * @see \SessionHandlerInterface 底层接口参考
 */
interface ISession
{
    /**
     * 默认 Session 名字.
     */
    public const SESSION_NAME = 'UID';

    /**
     * 闪存值键前缀.
     */
    public const FLASH_DATA_KEY_PREFIX = 'flash.data.';

    /**
     * 新值闪存键.
     */
    public const FLASH_NEW_KEY = 'flash.new.key';

    /**
     * 旧值闪存键.
     */
    public const FLASH_OLD_KEY = 'flash.old.key';

    /**
     * 前一个页面键.
     */
    public const PREV_URL_KEY = 'prev.url.key';

    /**
     * 启动 Session.
     */
    public function start(?string $sessionId = null): void;

    /**
     * 程序执行保存 Session.
     */
    public function save(): void;

    /**
     * 设置过期时间.
     */
    public function setExpire(?int $expire = null): void;

    /**
     * 取回所有 Session 数据.
     */
    public function all(): array;

    /**
     * 设置 Session.
     */
    public function set(string $name, mixed $value): void;

    /**
     * 批量插入.
     */
    public function put(array|string $keys, mixed $value = null): void;

    /**
     * 取回 Session.
     */
    public function get(string $name, mixed $defaults = null): mixed;

    /**
     * 删除 Session.
     */
    public function delete(string $name): void;

    /**
     * 是否存在 Session.
     */
    public function has(string $name): bool;

    /**
     * 清空 Session.
     */
    public function clear(): void;

    /**
     * 闪存一个数据，当前请求和下一个请求可用.
     */
    public function flash(string $key, mixed $value): void;

    /**
     * 批量闪存数据，当前请求和下一个请求可用.
     */
    public function flashs(array $flash): void;

    /**
     * 闪存数据用于当前请求使用，下一个请求将无法获取.
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
     * 程序执行结束清理闪存数据.
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
     * Session 是否已经启动.
     */
    public function isStart(): bool;

    /**
     * 设置 Session 名字.
     */
    public function setName(string $name): void;

    /**
     * 取得 Session 名字.
     */
    public function getName(): ?string;

    /**
     * 设置 Session ID.
     */
    public function setId(?string $id = null): void;

    /**
     * 取得 Session ID.
     */
    public function getId(): string;

    /**
     * 重新生成 Session ID.
     */
    public function regenerateId(): string;

    /**
     * Open.
     */
    public function open(string $savePath, string $sessionName): bool;

    /**
     * Close.
     */
    public function close(): bool;

    /**
     * Read.
     */
    public function read(string $sessionId): string;

    /**
     * Write.
     */
    public function write(string $sessionId, string $sessionData): bool;

    /**
     * Destroy.
     */
    public function destroy(string $sessionId): bool;

    /**
     * Gc.
     */
    public function gc(int $maxLifetime): int;
}
