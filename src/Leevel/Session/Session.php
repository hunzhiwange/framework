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
use function Leevel\Support\Str\rand_alpha_num;
use Leevel\Support\Str\rand_alpha_num;
use RuntimeException;

/**
 * session 驱动抽象类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @see http://php.net/manual/zh/class.sessionhandlerinterface.php
 * @since 2017.04.17
 *
 * @version 1.0
 */
abstract class Session
{
    /**
     * 缓存仓储.
     *
     * @var \Leevel\Cache\ICache
     */
    protected $cache;

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
    protected $data = [];

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [];

    /**
     * 构造函数.
     *
     * @param \Leevel\Cache\ICache $cache
     */
    public function __construct(ICache $cache)
    {
        $this->cache = $cache;

        $this->setName($this->option['name']);
    }

    /**
     * 启动 session.
     *
     * @param string $sessionId
     */
    public function start(?string $sessionId = null): void
    {
        if ($this->isStart()) {
            return;
        }

        $this->setId($sessionId ?: $this->option['id']);

        $this->loadData();

        $this->started = true;
    }

    /**
     * 程序执行保存 session.
     */
    public function save(): void
    {
        if (!$this->isStart()) {
            throw new RuntimeException('Session is not start yet.');
        }

        $this->unregisterFlash();

        $this->write($this->getId(), serialize($this->data));

        $this->started = false;
    }

    /**
     * 取回所有 session 数据.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * 设置 session.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set(string $name, $value): void
    {
        $name = $this->getNormalizeName($name);

        $this->data[$name] = $value;
    }

    /**
     * 批量插入.
     *
     * @param array|string $keys
     * @param mixed        $value
     */
    public function put($keys, $value = null): void
    {
        if (!is_array($keys)) {
            $keys = [$keys => $value];
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
    public function push(string $key, $value): void
    {
        $data = $this->get($key, []);
        $data[] = $value;
        $this->set($key, $data);
    }

    /**
     * 合并元素.
     *
     * @param string $key
     * @param array  $value
     */
    public function merge(string $key, array $value): void
    {
        $this->set($key, array_merge($this->get($key, []), $value));
    }

    /**
     * 弹出元素.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function pop(string $key, array $value): void
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
    public function arr(string $key, $keys, $value = null): void
    {
        $data = $this->get($key, []);

        if (is_string($keys)) {
            $data[$keys] = $value;
        } elseif (is_array($keys)) {
            $data = array_merge($data, $keys);
        }

        $this->set($key, $data);
    }

    /**
     * 数组键值删除数据.
     *
     * @param string $key
     * @param mixed  $keys
     */
    public function arrDelete(string $key, $keys): void
    {
        $data = $this->get($key, []);

        if (!is_array($keys)) {
            $keys = [$keys];
        }

        foreach ($keys as $item) {
            if (isset($data[$item])) {
                unset($data[$item]);
            }
        }

        $this->set($key, $data);
    }

    /**
     * 取回 session.
     *
     * @param string $name
     * @param mixed  $defaults
     *
     * @return mixed
     */
    public function get(string $name, $defaults = null)
    {
        $name = $this->getNormalizeName($name);

        return $this->data[$name] ?? $defaults;
    }

    /**
     * 返回数组部分数据.
     *
     * @param string $name
     * @param mixed  $defaults
     *
     * @return mixed
     */
    public function getPart(string $name, $defaults = null)
    {
        return $this->getPartData($name, $defaults);
    }

    /**
     * 删除 session.
     *
     * @param string $name
     */
    public function delete(string $name): void
    {
        $name = $this->getNormalizeName($name);

        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
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
        $name = $this->getNormalizeName($name);

        return isset($this->data[$name]);
    }

    /**
     * 删除 session.
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * 闪存一个数据，当前请求和下一个请求可用.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function flash(string $key, $value): void
    {
        $this->set($this->flashDataKey($key), $value);

        $this->mergeNewFlash([$key]);

        $this->popOldFlash([$key]);
    }

    /**
     * 批量闪存数据，当前请求和下一个请求可用.
     *
     * @param array $flash
     */
    public function flashs(array $flash): void
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
    public function nowFlash(string $key, $value): void
    {
        $this->set($this->flashDataKey($key), $value);

        $this->mergeOldFlash([$key]);
    }

    /**
     * 批量闪存数据,用于当前请求使用，下一个请求将无法获取.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function nowFlashs(array $flash): void
    {
        foreach ($flash as $key => $value) {
            $this->nowFlash($key, $value);
        }
    }

    /**
     * 保持所有闪存数据.
     */
    public function rebuildFlash(): void
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
    public function keepFlash(array $keys): void
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
    public function deleteFlash(array $keys): void
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
    public function clearFlash(): void
    {
        $this->deleteFlash($this->get($this->flashNewKey(), []));
    }

    /**
     * 程序执行结束清理 flash.
     */
    public function unregisterFlash(): void
    {
        $data = $this->get($this->flashNewKey(), []);
        $old = $this->get($this->flashOldKey(), []);

        foreach ($old as $item) {
            $this->delete($this->flashDataKey($item));
        }

        $this->delete($this->flashNewKey());
        $this->set($this->flashOldKey(), $data);
    }

    /**
     * 获取前一个请求地址.
     *
     * @return null|string
     */
    public function prevUrl(): ?string
    {
        return $this->get($this->prevUrlKey());
    }

    /**
     * 设置前一个请求地址
     *
     * @param string $url
     */
    public function setPrevUrl(string $url): void
    {
        $this->set($this->prevUrlKey(), $url);
    }

    /**
     * 终止会话.
     */
    public function destroySession(): void
    {
        $this->clear();
        $this->destroy($this->getId());

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
    public function setName(?string $name = null): void
    {
        $this->name = $name ?: ISession::SESSION_NAME;
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
    public function setId(?string $id = null): void
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
     * open.
     *
     * @param string $savePath
     * @param string $sessionName
     *
     * @return bool
     */
    public function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    /**
     * close.
     *
     * @return bool
     */
    public function close(): bool
    {
        return true;
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
        return serialize($this->cache->get(
            $this->getSessionName($sessionId), []
        ) ?: []);
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
        $this->cache->set(
            $this->getSessionName($sessionId),
            unserialize($sessionData)
        );

        return true;
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
        $this->cache->delete(
            $this->getSessionName($sessionId)
        );

        return true;
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
        return 0;
    }

    /**
     * 获取 session 名字.
     *
     * @param string $sessionId
     *
     * @return string
     */
    protected function getSessionName(string $sessionId): string
    {
        return $sessionId;
    }

    /**
     * 生成 SESSION ID.
     *
     * @return string
     */
    protected function generateSessionId(): string
    {
        return sha1($this->parseMicrotime().'.'.time().'.'.rand_alpha_num(32));
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
    protected function getNormalizeName(string $name): string
    {
        return $name;
    }

    /**
     * 载入 session 数据.
     */
    protected function loadData(): void
    {
        $this->data = array_merge($this->data, $this->loadDataFromConnect());
    }

    /**
     * 从驱动载入 session 数据.
     *
     * @return array
     */
    protected function loadDataFromConnect(): array
    {
        return unserialize($this->read($this->getId()));
    }

    /**
     * 弹出旧闪存 KEY.
     *
     * @param array $keys
     */
    protected function popOldFlash(array $keys): void
    {
        $this->pop($this->flashOldKey(), $keys);
    }

    /**
     * 合并旧闪存 KEY.
     *
     * @param array $keys
     */
    protected function mergeOldFlash(array $keys): void
    {
        $this->merge($this->flashOldKey(), $keys);
    }

    /**
     * 弹出新闪存 KEY.
     *
     * @param array $keys
     */
    protected function popNewFlash(array $keys): void
    {
        $this->pop($this->flashNewKey(), $keys);
    }

    /**
     * 合并新闪存 KEY.
     *
     * @param array $keys
     */
    protected function mergeNewFlash(array $keys): void
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
    protected function flashDataKey(string $key): string
    {
        return 'flash.data.'.$key;
    }

    /**
     * 新值闪存 KEY.
     *
     * @return string
     */
    protected function flashNewKey(): string
    {
        return 'flash.new.key';
    }

    /**
     * 旧值闪存 KEY.
     *
     * @return string
     */
    protected function flashOldKey(): string
    {
        return 'flash.old.key';
    }

    /**
     * 前一个页面 KEY.
     *
     * @return string
     */
    protected function prevUrlKey(): string
    {
        return 'prev.url.key';
    }
}

// import fn.
class_exists(rand_alpha_num::class);
