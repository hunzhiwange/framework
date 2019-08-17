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

/**
 * 代理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.11
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
trait Proxy
{
    /**
     * 启动 session.
     *
     * @param null|string $sessionId
     */
    public function start(?string $sessionId = null): void
    {
        $this->proxy()->start($sessionId);
    }

    /**
     * 程序执行保存 session.
     */
    public function save(): void
    {
        $this->proxy()->save();
    }

    /**
     * 取回所有 session 数据.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->proxy()->all();
    }

    /**
     * 设置 session.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set(string $name, $value): void
    {
        $this->proxy()->set($name, $value);
    }

    /**
     * 批量插入.
     *
     * @param array|string $keys
     * @param null|mixed   $value
     */
    public function put($keys, $value = null): void
    {
        $this->proxy()->put($keys, $value);
    }

    /**
     * 数组插入数据.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function push(string $key, $value): void
    {
        $this->proxy()->push($key, $value);
    }

    /**
     * 合并元素.
     *
     * @param string $key
     * @param array  $value
     */
    public function merge(string $key, array $value): void
    {
        $this->proxy()->merge($key, $value);
    }

    /**
     * 弹出元素.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function pop(string $key, array $value): void
    {
        $this->proxy()->pop($key, $value);
    }

    /**
     * 数组插入键值对数据.
     *
     * @param string     $key
     * @param mixed      $keys
     * @param null|mixed $value
     */
    public function arr(string $key, $keys, $value = null): void
    {
        $this->proxy()->arr($key, $keys, $value);
    }

    /**
     * 数组键值删除数据.
     *
     * @param string $key
     * @param mixed  $keys
     */
    public function arrDelete(string $key, $keys): void
    {
        $this->proxy()->arrDelete($key, $keys);
    }

    /**
     * 取回 session.
     *
     * @param string     $name
     * @param null|mixed $value
     *
     * @return mixed
     */
    public function get(string $name, $value = null)
    {
        return $this->proxy()->get($name, $value);
    }

    /**
     * 返回数组部分数据.
     *
     * @param string     $name
     * @param null|mixed $value
     *
     * @return mixed
     */
    public function getPart(string $name, $value = null)
    {
        return $this->proxy()->getPart($name, $value);
    }

    /**
     * 删除 session.
     *
     * @param string $name
     */
    public function delete(string $name): void
    {
        $this->proxy()->delete($name);
    }

    /**
     * 是否存在 session.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return $this->proxy()->has($name);
    }

    /**
     * 删除 session.
     */
    public function clear(): void
    {
        $this->proxy()->clear();
    }

    /**
     * 闪存一个数据，当前请求和下一个请求可用.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function flash(string $key, $value): void
    {
        $this->proxy()->flash($key, $value);
    }

    /**
     * 批量闪存数据，当前请求和下一个请求可用.
     *
     * @param array $flash
     */
    public function flashs(array $flash): void
    {
        $this->proxy()->flashs($flash);
    }

    /**
     * 闪存一个 flash 用于当前请求使用，下一个请求将无法获取.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function nowFlash(string $key, $value): void
    {
        $this->proxy()->nowFlash($key, $value);
    }

    /**
     * 保持所有闪存数据.
     */
    public function rebuildFlash(): void
    {
        $this->proxy()->rebuildFlash();
    }

    /**
     * 保持闪存数据.
     *
     * @param array $keys
     */
    public function keepFlash(array $keys): void
    {
        $this->proxy()->keepFlash($keys);
    }

    /**
     * 返回闪存数据.
     *
     * @param string     $key
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public function getFlash(string $key, $defaults = null)
    {
        return $this->proxy()->getFlash($key, $defaults);
    }

    /**
     * 删除闪存数据.
     *
     * @param array $keys
     */
    public function deleteFlash(array $keys): void
    {
        $this->proxy()->deleteFlash($keys);
    }

    /**
     * 清理所有闪存数据.
     */
    public function clearFlash(): void
    {
        $this->proxy()->clearFlash();
    }

    /**
     * 程序执行结束清理 flash.
     */
    public function unregisterFlash(): void
    {
        $this->proxy()->unregisterFlash();
    }

    /**
     * 获取前一个请求地址
     *
     * @return null|string
     */
    public function prevUrl(): ?string
    {
        return $this->proxy()->prevUrl();
    }

    /**
     * 设置前一个请求地址
     *
     * @param string $url
     */
    public function setPrevUrl(string $url): void
    {
        $this->proxy()->setPrevUrl($url);
    }

    /**
     * 终止会话.
     */
    public function destroySession(): void
    {
        $this->proxy()->destroySession();
    }

    /**
     * session 是否已经启动.
     *
     * @return bool
     */
    public function isStart(): bool
    {
        return $this->proxy()->isStart();
    }

    /**
     * 设置 SESSION 名字.
     *
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->proxy()->setName($name);
    }

    /**
     * 取得 SESSION 名字.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->proxy()->getName();
    }

    /**
     * 设置 SESSION ID.
     *
     * @param null|string $id
     */
    public function setId(?string $id = null): void
    {
        $this->proxy()->setId($id);
    }

    /**
     * 取得 SESSION ID.
     *
     * @return string
     */
    public function getId(): ?string
    {
        return $this->proxy()->getId();
    }

    /**
     * 重新生成 SESSION ID.
     */
    public function regenerateId(): string
    {
        return $this->proxy()->regenerateId();
    }

    /**
     * open.
     *
     * @param string $savePath
     * @param string $sessionName
     *
     * @return bool
     */
    public function open(string $savePath, string $sessionName): bool
    {
        return $this->proxy()->open($savePath, $sessionName);
    }

    /**
     * close.
     *
     * @return bool
     */
    public function close(): bool
    {
        return $this->proxy()->close();
    }

    /**
     * read.
     *
     * @param string $sessionId
     *
     * @return string
     */
    public function read(string $sessionId): string
    {
        return $this->proxy()->read($sessionId);
    }

    /**
     * write.
     *
     * @param string $sessionId
     * @param string $sessionData
     *
     * @return bool
     */
    public function write(string $sessionId, string $sessionData): bool
    {
        return $this->proxy()->write($sessionId, $sessionData);
    }

    /**
     * destroy.
     *
     * @param string $sessionId
     *
     * @return bool
     */
    public function destroy(string $sessionId): bool
    {
        return $this->proxy()->destroy($sessionId);
    }

    /**
     * gc.
     *
     * @param int $maxLifetime
     *
     * @return int
     */
    public function gc(int $maxLifetime): int
    {
        return $this->proxy()->gc($maxLifetime);
    }

    /**
     * 返回代理.
     *
     * @return \Leevel\Session\ISession
     */
    public function proxy(): ISession
    {
        return $this->connect();
    }
}
