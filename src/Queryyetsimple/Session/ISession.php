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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Session;

use SessionHandlerInterface;

/**
 * ISession 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.11
 *
 * @version 1.0
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
    public function start(?string $sessionId = null);

    /**
     * 程序执行保存 session.
     */
    public function save();

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
     * @param mxied  $value
     */
    public function set(string $name, $value);

    /**
     * 批量插入.
     *
     * @param array|string $keys
     * @param mixed        $value
     */
    public function put($keys, $value = null);

    /**
     * 数组插入数据.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function push(string $key, $value);

    /**
     * 合并元素.
     *
     * @param string $key
     * @param array  $value
     */
    public function merge(string $key, array $value);

    /**
     * 弹出元素.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function pop(string $key, array $value);

    /**
     * 数组插入键值对数据.
     *
     * @param string $key
     * @param mixed  $keys
     * @param mixed  $value
     */
    public function arr(string $key, $keys, $value = null);

    /**
     * 数组键值删除数据.
     *
     * @param string $key
     * @param mixed  $keys
     */
    public function arrDelete(string $key, $keys);

    /**
     * 取回 session.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mxied
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
    public function delete(string $name);

    /**
     * 是否存在 session.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name);

    /**
     * 删除 session.
     */
    public function clear();

    /**
     * 闪存一个数据，当前请求和下一个请求可用.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function flash(string $key, $value);

    /**
     * 批量闪存数据，当前请求和下一个请求可用.
     *
     * @param array $flash
     */
    public function flashs(array $flash);

    /**
     * 闪存一个 flash 用于当前请求使用，下一个请求将无法获取.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function nowFlash(string $key, $value);

    /**
     * 保持所有闪存数据.
     */
    public function rebuildFlash();

    /**
     * 保持闪存数据.
     *
     * @param mixed $keys
     */
    public function keepFlash($keys);

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
     * @param mixed $keys
     */
    public function deleteFlash($keys);

    /**
     * 清理所有闪存数据.
     */
    public function clearFlash();

    /**
     * 程序执行结束清理 flash.
     */
    public function unregisterFlash();

    /**
     * 获取前一个请求地址
     *
     * @return null|string
     */
    public function prevUrl();

    /**
     * 设置前一个请求地址
     *
     * @param string $url
     */
    public function setPrevUrl(string $url);

    /**
     * 终止会话.
     */
    public function destroy();

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
    public function setName(string $name);

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
    public function setId(?string $id = null);

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
     * 返回连接.
     *
     * @return \SessionHandlerInterface
     */
    public function getConnect(): SessionHandlerInterface;
}
