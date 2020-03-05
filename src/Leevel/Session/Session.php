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

use Leevel\Cache\ICache;
use function Leevel\Support\Str\rand_alpha_num;
use Leevel\Support\Str\rand_alpha_num;
use RuntimeException;

/**
 * Session 抽象类.
 *
 * @see http://php.net/manual/zh/class.sessionhandlerinterface.php
 */
abstract class Session
{
    /**
     * 缓存仓储.
     *
     * @var \Leevel\Cache\ICache
     */
    protected ?ICache $cache = null;

    /**
     * session ID.
     *
     * - 相当于 session_id.
     *
     * @var string
     */
    protected ?string $id = null;

    /**
     * session 名字.
     *
     * - 相当于 session_name.
     *
     * @var string
     */
    protected ?string $name = null;

    /**
     * session 是否开启.
     *
     * @var bool
     */
    protected bool $started = false;

    /**
     * session 数据.
     *
     * @var array
     */
    protected array $data = [];

    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [];

    /**
     * 构造函数.
     */
    public function __construct(ICache $cache)
    {
        $this->cache = $cache;
        $this->setName($this->option['name']);
    }

    /**
     * 启动 session.
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
     *
     * @throws \RuntimeException
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
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * 设置 session.
     *
     * @param mixed $value
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
     * @param null|mixed   $value
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
     * 取回 session.
     *
     * @param null|mixed $defaults
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
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public function getPart(string $name, $defaults = null)
    {
        return $this->getPartData($name, $defaults);
    }

    /**
     * 删除 session.
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
     */
    public function has(string $name): bool
    {
        $name = $this->getNormalizeName($name);

        return isset($this->data[$name]);
    }

    /**
     * 清空 session.
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * 闪存一个数据，当前请求和下一个请求可用.
     *
     * @param mixed $value
     */
    public function flash(string $key, $value): void
    {
        $this->set($this->flashDataKey($key), $value);
        $this->mergeNewFlash([$key]);
        $this->popOldFlash([$key]);
    }

    /**
     * 批量闪存数据，当前请求和下一个请求可用.
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
     * @param mixed $value
     */
    public function nowFlash(string $key, $value): void
    {
        $this->set($this->flashDataKey($key), $value);
        $this->mergeOldFlash([$key]);
    }

    /**
     * 批量闪存数据,用于当前请求使用，下一个请求将无法获取.
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
            $this->get(ISession::FLASH_OLD_KEY, [])
        );
        $this->set(ISession::FLASH_OLD_KEY, []);
    }

    /**
     * 保持闪存数据.
     */
    public function keepFlash(array $keys): void
    {
        $this->mergeNewFlash($keys);
        $this->popOldFlash($keys);
    }

    /**
     * 返回闪存数据.
     *
     * @param null|mixed $defaults
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
        $this->deleteFlash($this->get(ISession::FLASH_NEW_KEY, []));
    }

    /**
     * 程序执行结束清理 flash.
     */
    public function unregisterFlash(): void
    {
        $data = $this->get(ISession::FLASH_NEW_KEY, []);
        $old = $this->get(ISession::FLASH_OLD_KEY, []);
        foreach ($old as $item) {
            $this->delete($this->flashDataKey($item));
        }
        $this->delete(ISession::FLASH_NEW_KEY);
        $this->set(ISession::FLASH_OLD_KEY, $data);
    }

    /**
     * 获取前一个请求地址.
     */
    public function prevUrl(): ?string
    {
        return $this->get(ISession::PREV_URL_KEY);
    }

    /**
     * 设置前一个请求地址
     */
    public function setPrevUrl(string $url): void
    {
        $this->set(ISession::PREV_URL_KEY, $url);
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
     */
    public function isStart(): bool
    {
        return $this->started;
    }

    /**
     * 设置 SESSION 名字.
     */
    public function setName(?string $name = null): void
    {
        $this->name = $name ?: ISession::SESSION_NAME;
    }

    /**
     * 取得 SESSION 名字.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * 设置 SESSION ID.
     */
    public function setId(?string $id = null): void
    {
        $this->id = $id ?: $this->generateSessionId();
    }

    /**
     * 取得 SESSION ID.
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * 重新生成 SESSION ID.
     */
    public function regenerateId(): string
    {
        return $this->id = $this->generateSessionId();
    }

    /**
     * open.
     */
    public function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    /**
     * close.
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * read.
     */
    public function read(string $sessionId): string
    {
        return serialize($this->cache->get(
            $this->getSessionName($sessionId), []
        ) ?: []);
    }

    /**
     * write.
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
     */
    public function gc(int $maxLifetime): int
    {
        return 0;
    }

    /**
     * 合并元素.
     */
    protected function mergeItem(string $key, array $value, array $option = []): void
    {
        $this->set($key, array_merge($this->get($key, [], $option), $value), $option);
    }

    /**
     * 弹出元素.
     *
     * @param mixed $value
     */
    protected function popItem(string $key, array $value, array $option = []): void
    {
        $this->set($key, array_diff($this->get($key, [], $option), $value), $option);
    }

    /**
     * 获取 session 名字.
     */
    protected function getSessionName(string $sessionId): string
    {
        return $sessionId;
    }

    /**
     * 生成 SESSION ID.
     */
    protected function generateSessionId(): string
    {
        return sha1($this->parseMicrotime().'.'.time().'.'.rand_alpha_num(32));
    }

    /**
     * 生成微秒数.
     */
    protected function parseMicrotime(): string
    {
        list($usec, $sec) = explode(' ', microtime());

        return (string) ((float) $usec + (float) $sec);
    }

    /**
     * 返回 session 名字.
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
     */
    protected function loadDataFromConnect(): array
    {
        return unserialize($this->read($this->getId()));
    }

    /**
     * 弹出旧闪存 KEY.
     */
    protected function popOldFlash(array $keys): void
    {
        $this->popItem(ISession::FLASH_OLD_KEY, $keys);
    }

    /**
     * 合并旧闪存 KEY.
     */
    protected function mergeOldFlash(array $keys): void
    {
        $this->mergeItem(ISession::FLASH_OLD_KEY, $keys);
    }

    /**
     * 弹出新闪存 KEY.
     */
    protected function popNewFlash(array $keys): void
    {
        $this->popItem(ISession::FLASH_NEW_KEY, $keys);
    }

    /**
     * 合并新闪存 KEY.
     */
    protected function mergeNewFlash(array $keys): void
    {
        $this->mergeItem(ISession::FLASH_NEW_KEY, $keys);
    }

    /**
     * 返回部分闪存数据.
     *
     * @param null|mixed $defaults
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
     */
    protected function flashDataKey(string $key): string
    {
        return ISession::FLASH_DATA_KEY_PREFIX.$key;
    }
}

// import fn.
class_exists(rand_alpha_num::class);
