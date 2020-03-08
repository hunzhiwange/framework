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
 * @codeCoverageIgnore
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
     * 启动 session.
     */
    public static function start(?string $sessionId = null): void
    {
        self::proxy()->start($sessionId);
    }

    /**
     * 程序执行保存 session.
     */
    public static function save(): void
    {
        self::proxy()->save();
    }

    /**
     * 取回所有 session 数据.
     */
    public static function all(): array
    {
        return self::proxy()->all();
    }

    /**
     * 设置 session.
     *
     * @param mixed $value
     */
    public static function set(string $name, $value): void
    {
        self::proxy()->set($name, $value);
    }

    /**
     * 批量插入.
     *
     * @param array|string $keys
     * @param null|mixed   $value
     */
    public static function put($keys, $value = null): void
    {
        self::proxy()->put($keys, $value);
    }

    /**
     * 取回 session.
     *
     * @param null|mixed $value
     *
     * @return mixed
     */
    public static function get(string $name, $value = null)
    {
        return self::proxy()->get($name, $value);
    }

    /**
     * 删除 session.
     */
    public static function delete(string $name): void
    {
        self::proxy()->delete($name);
    }

    /**
     * 是否存在 session.
     */
    public static function has(string $name): bool
    {
        return self::proxy()->has($name);
    }

    /**
     * 删除 session.
     */
    public static function clear(): void
    {
        self::proxy()->clear();
    }

    /**
     * 闪存一个数据，当前请求和下一个请求可用.
     *
     * @param mixed $value
     */
    public static function flash(string $key, $value): void
    {
        self::proxy()->flash($key, $value);
    }

    /**
     * 批量闪存数据，当前请求和下一个请求可用.
     */
    public static function flashs(array $flash): void
    {
        self::proxy()->flashs($flash);
    }

    /**
     * 闪存一个 flash 用于当前请求使用，下一个请求将无法获取.
     *
     * @param mixed $value
     */
    public static function nowFlash(string $key, $value): void
    {
        self::proxy()->nowFlash($key, $value);
    }

    /**
     * 保持所有闪存数据.
     */
    public static function rebuildFlash(): void
    {
        self::proxy()->rebuildFlash();
    }

    /**
     * 保持闪存数据.
     */
    public static function keepFlash(array $keys): void
    {
        self::proxy()->keepFlash($keys);
    }

    /**
     * 返回闪存数据.
     *
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public static function getFlash(string $key, $defaults = null)
    {
        return self::proxy()->getFlash($key, $defaults);
    }

    /**
     * 删除闪存数据.
     */
    public static function deleteFlash(array $keys): void
    {
        self::proxy()->deleteFlash($keys);
    }

    /**
     * 清理所有闪存数据.
     */
    public static function clearFlash(): void
    {
        self::proxy()->clearFlash();
    }

    /**
     * 程序执行结束清理 flash.
     */
    public static function unregisterFlash(): void
    {
        self::proxy()->unregisterFlash();
    }

    /**
     * 获取前一个请求地址
     */
    public static function prevUrl(): ?string
    {
        return self::proxy()->prevUrl();
    }

    /**
     * 设置前一个请求地址
     */
    public static function setPrevUrl(string $url): void
    {
        self::proxy()->setPrevUrl($url);
    }

    /**
     * 终止会话.
     */
    public static function destroySession(): void
    {
        self::proxy()->destroySession();
    }

    /**
     * session 是否已经启动.
     */
    public static function isStart(): bool
    {
        return self::proxy()->isStart();
    }

    /**
     * 设置 SESSION 名字.
     */
    public static function setName(string $name): void
    {
        self::proxy()->setName($name);
    }

    /**
     * 取得 SESSION 名字.
     *
     * @return string
     */
    public static function getName(): ?string
    {
        return self::proxy()->getName();
    }

    /**
     * 设置 SESSION ID.
     */
    public static function setId(?string $id = null): void
    {
        self::proxy()->setId($id);
    }

    /**
     * 取得 SESSION ID.
     *
     * @return string
     */
    public static function getId(): ?string
    {
        return self::proxy()->getId();
    }

    /**
     * 重新生成 SESSION ID.
     */
    public static function regenerateId(): string
    {
        return self::proxy()->regenerateId();
    }

    /**
     * open.
     */
    public static function open(string $savePath, string $sessionName): bool
    {
        return self::proxy()->open($savePath, $sessionName);
    }

    /**
     * close.
     */
    public static function close(): bool
    {
        return self::proxy()->close();
    }

    /**
     * read.
     */
    public static function read(string $sessionId): string
    {
        return self::proxy()->read($sessionId);
    }

    /**
     * write.
     */
    public static function write(string $sessionId, string $sessionData): bool
    {
        return self::proxy()->write($sessionId, $sessionData);
    }

    /**
     * destroy.
     */
    public static function destroy(string $sessionId): bool
    {
        return self::proxy()->destroy($sessionId);
    }

    /**
     * gc.
     */
    public static function gc(int $maxLifetime): int
    {
        return self::proxy()->gc($maxLifetime);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        return Container::singletons()->make('sessions');
    }
}
