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

use Leevel\Support\Str;
use RuntimeException;
use SessionHandlerInterface;

/**
 * session 仓储.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.17
 *
 * @version 1.0
 */
class Session implements ISession
{
    /**
     * session ID.
     * 相当于 session_id.
     *
     * @var string
     */
    protected $id;

    /**
     * session 名字.
     * 相当于 session_name.
     *
     * @var string
     */
    protected $name;

    /**
     * session handler.
     *
     * @var \SessionHandlerInterface
     */
    protected $connect;

    /**
     * session 是否开启.
     *
     * @var bool
     */
    protected $started = false;

    /**
     * session 数据.
     *
     * @var array
     */
    protected $datas = [];

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'id'             => null,
        'name'           => null,
    ];

    /**
     * 构造函数.
     *
     * @param \SessionHandlerInterface $connect
     * @param array                    $option
     */
    public function __construct(SessionHandlerInterface $connect, array $option = [])
    {
        $this->connect = $connect;

        $this->option = array_merge($this->option, $option);

        $this->setName($this->option['name']);
    }

    /**
     * 启动 session.
     *
     * @param string $sessionId
     */
    public function start(?string $sessionId = null)
    {
        if ($this->isStart()) {
            return $this;
        }

        $this->setId($sessionId ?: $this->option['id']);

        $this->loadData();

        $this->started = true;
    }

    /**
     * 程序执行保存 session.
     */
    public function save()
    {
        if (!$this->isStart()) {
            throw new RuntimeException(
                'Session is not start yet.'
            );
        }

        $this->unregisterFlash();

        $this->connect->write($this->getId(), serialize($this->datas));

        $this->started = false;
    }

    /**
     * 取回所有 session 数据.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->datas;
    }

    /**
     * 设置 session.
     *
     * @param string $name
     * @param mxied  $value
     */
    public function set(string $name, $value)
    {
        $name = $this->getNormalizeName($name);

        $this->datas[$name] = $value;
    }

    /**
     * 批量插入.
     *
     * @param array|string $keys
     * @param mixed        $value
     */
    public function put($keys, $value = null)
    {
        if (!is_array($keys)) {
            $keys = [
                $keys => $value,
            ];
        }

        foreach ($keys as $item => $value) {
            $this->set($item, $value);
        }
    }

    /**
     * 数组插入数据.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function push(string $key, $value)
    {
        $arr = $this->get($key, []);
        $arr[] = $value;

        $this->set($key, $arr);
    }

    /**
     * 合并元素.
     *
     * @param string $key
     * @param array  $value
     */
    public function merge(string $key, array $value)
    {
        $this->set($key, array_merge($this->get($key, []), $value));
    }

    /**
     * 弹出元素.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function pop(string $key, array $value)
    {
        $this->set($key, array_diff($this->get($key, []), $value));
    }

    /**
     * 数组插入键值对数据.
     *
     * @param string $key
     * @param mixed  $keys
     * @param mixed  $value
     */
    public function arr(string $key, $keys, $value = null)
    {
        $arr = $this->get($key, []);

        if (is_string($keys)) {
            $arr[$keys] = $value;
        } elseif (is_array($keys)) {
            $arr = array_merge($arr, $keys);
        }

        $this->set($key, $arr);
    }

    /**
     * 数组键值删除数据.
     *
     * @param string $key
     * @param mixed  $keys
     */
    public function arrDelete(string $key, $keys)
    {
        $arr = $this->get($key, []);

        if (!is_array($keys)) {
            $keys = [
                $keys,
            ];
        }

        foreach ($keys as $item) {
            if (isset($arr[$item])) {
                unset($arr[$item]);
            }
        }

        $this->set($key, $arr);
    }

    /**
     * 取回 session.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mxied
     */
    public function get(string $name, $value = null)
    {
        $name = $this->getNormalizeName($name);

        return $this->datas[$name] ?? $value;
    }

    /**
     * 返回数组部分数据.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function getPart(string $name, $value = null)
    {
        return $this->getPartData($name, $value);
    }

    /**
     * 删除 session.
     *
     * @param string $name
     */
    public function delete(string $name)
    {
        $name = $this->getNormalizeName($name);

        if (isset($this->datas[$name])) {
            unset($this->datas[$name]);
        }
    }

    /**
     * 是否存在 session.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name)
    {
        $name = $this->getNormalizeName($name);

        return isset($this->datas[$name]);
    }

    /**
     * 删除 session.
     */
    public function clear()
    {
        $this->datas = [];
    }

    /**
     * 闪存一个数据，当前请求和下一个请求可用.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function flash(string $key, $value)
    {
        $this->set($this->flashDataKey($key), $value);

        $this->mergeNewFlash([
            $key,
        ]);

        $this->popOldFlash([
            $key,
        ]);
    }

    /**
     * 批量闪存数据，当前请求和下一个请求可用.
     *
     * @param array $flash
     */
    public function flashs(array $flash)
    {
        foreach ($flash as $key => $value) {
            $this->flash($key, $value);
        }
    }

    /**
     * 闪存一个 flash 用于当前请求使用,下一个请求将无法获取.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function nowFlash(string $key, $value)
    {
        $this->set($this->flashDataKey($key), $value);

        $this->mergeOldFlash([
            $key,
        ]);
    }

    /**
     * 批量闪存数据,用于当前请求使用，下一个请求将无法获取.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function nowFlashs(array $flash)
    {
        foreach ($flash as $key => $value) {
            $this->nowFlash($key, $value);
        }
    }

    /**
     * 保持所有闪存数据.
     */
    public function rebuildFlash()
    {
        $this->mergeNewFlash(
            $this->get($this->flashOldKey(), [])
        );

        $this->set($this->flashOldKey(), []);
    }

    /**
     * 保持闪存数据.
     *
     * @param array $keys
     */
    public function keepFlash(array $keys)
    {
        $this->mergeNewFlash($keys);

        $this->popOldFlash($keys);
    }

    /**
     * 返回闪存数据.
     *
     * @param string $key
     * @param mixed  $defaults
     *
     * @return mixed
     */
    public function getFlash(string $key, $defaults = null)
    {
        if (false !== strpos($key, '\\')) {
            return $this->getPartData($key, $defaults, 'flash');
        }

        return $this->get(
            $this->flashDataKey($key),
            $defaults
        );
    }

    /**
     * 删除闪存数据.
     *
     * @param array $keys
     */
    public function deleteFlash(array $keys)
    {
        foreach ($keys as $item) {
            $this->delete($this->flashDataKey($item));
        }

        $this->mergeOldFlash($keys);

        $this->popNewFlash($keys);
    }

    /**
     * 清理所有闪存数据.
     */
    public function clearFlash()
    {
        $this->deleteFlash($this->get($this->flashNewKey(), []));
    }

    /**
     * 程序执行结束清理 flash.
     */
    public function unregisterFlash()
    {
        $arr = $this->get($this->flashNewKey(), []);
        $old = $this->get($this->flashOldKey(), []);

        foreach ($old as $item) {
            $this->delete($this->flashDataKey($item));
        }

        $this->delete($this->flashNewKey());
        $this->set($this->flashOldKey(), $arr);

        unset($arr, $old);
    }

    /**
     * 获取前一个请求地址
     *
     * @return null|string
     */
    public function prevUrl()
    {
        return $this->get($this->prevUrlKey());
    }

    /**
     * 设置前一个请求地址
     *
     * @param string $url
     */
    public function setPrevUrl(string $url)
    {
        return $this->set($this->prevUrlKey(), $url);
    }

    /**
     * 终止会话.
     */
    public function destroy()
    {
        $this->clear();
        $this->connect->destroy($this->getId());

        $this->id = null;
        $this->started = false;
    }

    /**
     * session 是否已经启动.
     *
     * @return bool
     */
    public function isStart(): bool
    {
        return $this->started;
    }

    /**
     * 设置 SESSION 名字.
     *
     * @param string $name
     */
    public function setName(?string $name = null)
    {
        $this->name = $name ?: static::SESSION_NAME;
    }

    /**
     * 取得 SESSION 名字.
     *
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * 设置 SESSION ID.
     *
     * @param string $id
     */
    public function setId(?string $id = null)
    {
        $this->id = $id ?: $this->generateSessionId();
    }

    /**
     * 取得 SESSION ID.
     *
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * 重新生成 SESSION ID.
     *
     * @return string
     */
    public function regenerateId(): string
    {
        return $this->id = $this->generateSessionId();
    }

    /**
     * 返回连接.
     *
     * @return \SessionHandlerInterface
     */
    public function getConnect(): SessionHandlerInterface
    {
        return $this->connect;
    }

    /**
     * 生成 SESSION ID.
     *
     * @return string
     */
    protected function generateSessionId(): string
    {
        return sha1($this->parseMicrotime().'.'.time().'.'.Str::randAlphaNum(32));
    }

    /**
     * 生成微秒数.
     *
     * @return string
     */
    protected function parseMicrotime(): string
    {
        list($usec, $sec) = explode(' ', microtime());

        return (string) ((float) $usec + (float) $sec);
    }

    /**
     * 返回 session 名字.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getNormalizeName(string $name)
    {
        return $name;
    }

    /**
     * 载入 session 数据.
     */
    protected function loadData()
    {
        $this->datas = array_merge($this->datas, $this->loadDataFromConnect());
    }

    /**
     * 从驱动载入 session 数据.
     */
    protected function loadDataFromConnect()
    {
        return $this->connect->read($this->getId());
    }

    /**
     * 弹出旧闪存 KEY.
     *
     * @param array $keys
     */
    protected function popOldFlash(array $keys)
    {
        $this->pop($this->flashOldKey(), $keys);
    }

    /**
     * 合并旧闪存 KEY.
     *
     * @param array $keys
     */
    protected function mergeOldFlash(array $keys)
    {
        $this->merge($this->flashOldKey(), $keys);
    }

    /**
     * 弹出新闪存 KEY.
     *
     * @param array $keys
     */
    protected function popNewFlash(array $keys)
    {
        $this->pop($this->flashNewKey(), $keys);
    }

    /**
     * 合并新闪存 KEY.
     *
     * @param array $keys
     */
    protected function mergeNewFlash(array $keys)
    {
        $this->merge($this->flashNewKey(), $keys);
    }

    /**
     * 返回部分闪存数据.
     *
     * @param string $key
     * @param mixed  $defaults
     * @param string $type
     *
     * @return mixed
     */
    protected function getPartData(string $key, $defaults = null, ?string $type = null)
    {
        list($key, $name) = explode('\\', $key);

        if ('flash' === $type) {
            $key = $this->flashDataKey($key);
        }

        $value = $this->get($key);

        if (is_array($value)) {
            if (!strpos($name, '.')) {
                return array_key_exists($name, $value) ? $value[$name] : $defaults;
            }

            $parts = explode('.', $name);

            foreach ($parts as $part) {
                if (!isset($value[$part])) {
                    return $defaults;
                }

                $value = $value[$part];
            }

            return $value;
        }

        return $defaults;
    }

    /**
     * 闪存值 KEY.
     *
     * @param string $key
     *
     * @return string
     */
    protected function flashDataKey(string $key)
    {
        return 'flash.data.'.$key;
    }

    /**
     * 新值闪存 KEY.
     *
     * @return string
     */
    protected function flashNewKey()
    {
        return 'flash.new.key';
    }

    /**
     * 旧值闪存 KEY.
     *
     * @return string
     */
    protected function flashOldKey()
    {
        return 'flash.old.key';
    }

    /**
     * 前一个页面 KEY.
     *
     * @return string
     */
    protected function prevUrlKey()
    {
        return 'prev.url.key';
    }
}
