<?php

declare(strict_types=1);

namespace Leevel\Session;

use Leevel\Cache\ICache;
use Leevel\Support\Str\RandAlphaNum;

/**
 * Session 抽象类.
 *
 * @see http://php.net/manual/zh/class.sessionhandlerinterface.php
 */
abstract class Session implements ISession
{
    /**
     * 缓存仓储.
     */
    protected ICache $cache;

    /**
     * session ID.
     *
     * - 相当于 session_id.
     */
    protected string $id = '';

    /**
     * session 名字.
     *
     * - 相当于 session_name.
     */
    protected ?string $name = null;

    /**
     * session 是否开启.
     */
    protected bool $started = false;

    /**
     * session 数据.
     */
    protected array $data = [];

    /**
     * 配置.
     */
    protected array $option = [];

    /**
     * 过期时间.
     */
    protected ?int $expire = null;

    /**
     * 构造函数.
     */
    public function __construct(ICache $cache)
    {
        $this->cache = $cache;
        $this->setName($this->option['name']);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     *
     * @throws \RuntimeException
     */
    public function save(): void
    {
        if (!$this->isStart()) {
            throw new \RuntimeException('Session is not start yet.');
        }

        $this->unregisterFlash();
        $this->write($this->getId(), serialize($this->data));
        $this->started = false;
    }

    /**
     * {@inheritDoc}
     */
    public function setExpire(?int $expire = null): void
    {
        $this->expire = $expire;
    }

    /**
     * {@inheritDoc}
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $name, mixed $value): void
    {
        $name = $this->getNormalizeName($name);
        $this->data[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function put(array|string $keys, mixed $value = null): void
    {
        if (!\is_array($keys)) {
            $keys = [$keys => $value];
        }

        foreach ($keys as $item => $value) {
            $this->set($item, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name, mixed $defaults = null): mixed
    {
        $name = $this->getNormalizeName($name);

        return $this->data[$name] ?? $defaults;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $name): void
    {
        $name = $this->getNormalizeName($name);
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        $name = $this->getNormalizeName($name);

        return isset($this->data[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * {@inheritDoc}
     */
    public function flash(string $key, mixed $value): void
    {
        $this->set($this->flashDataKey($key), $value);
        $this->mergeNewFlash([$key]);
        $this->popOldFlash([$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function flashs(array $flash): void
    {
        foreach ($flash as $key => $value) {
            $this->flash($key, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function nowFlash(string $key, mixed $value): void
    {
        $this->set($this->flashDataKey($key), $value);
        $this->mergeOldFlash([$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function nowFlashs(array $flash): void
    {
        foreach ($flash as $key => $value) {
            $this->nowFlash($key, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function rebuildFlash(): void
    {
        $this->mergeNewFlash(
            $this->get(ISession::FLASH_OLD_KEY, [])
        );
        $this->set(ISession::FLASH_OLD_KEY, []);
    }

    /**
     * {@inheritDoc}
     */
    public function keepFlash(array $keys): void
    {
        $this->mergeNewFlash($keys);
        $this->popOldFlash($keys);
    }

    /**
     * {@inheritDoc}
     */
    public function getFlash(string $key, mixed $defaults = null): mixed
    {
        return $this->get($this->flashDataKey($key), $defaults);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function clearFlash(): void
    {
        $this->deleteFlash($this->get(ISession::FLASH_NEW_KEY, []));
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function prevUrl(): ?string
    {
        return $this->get(ISession::PREV_URL_KEY);
    }

    /**
     * {@inheritDoc}
     */
    public function setPrevUrl(string $url): void
    {
        $this->set(ISession::PREV_URL_KEY, $url);
    }

    /**
     * {@inheritDoc}
     */
    public function destroySession(): void
    {
        $this->clear();
        $this->destroy($this->getId());
        $this->id = '';
        $this->started = false;
    }

    /**
     * {@inheritDoc}
     */
    public function isStart(): bool
    {
        return $this->started;
    }

    /**
     * {@inheritDoc}
     */
    public function setName(?string $name = null): void
    {
        $this->name = $name ?: ISession::SESSION_NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setId(?string $id = null): void
    {
        $this->id = $id ?: $this->generateSessionId();
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function regenerateId(): string
    {
        return $this->id = $this->generateSessionId();
    }

    /**
     * {@inheritDoc}
     */
    public function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function read(string $sessionId): string
    {
        $cacheData = $this->cache->get($this->getSessionName($sessionId), []);

        return serialize($cacheData ?: []);
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $sessionId, string $sessionData): bool
    {
        $this->cache->set(
            $this->getSessionName($sessionId),
            unserialize($sessionData),
            $this->expire
        );

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(string $sessionId): bool
    {
        $this->cache->delete(
            $this->getSessionName($sessionId)
        );

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function gc(int $maxLifetime): int
    {
        return 0;
    }

    /**
     * 合并元素.
     */
    protected function mergeItem(string $key, array $value): void
    {
        $this->set($key, array_merge($this->get($key, []), $value));
    }

    /**
     * 弹出元素.
     */
    protected function popItem(string $key, array $value): void
    {
        $this->set($key, array_diff($this->get($key, []), $value));
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
        return sha1($this->parseMicrotime().'.'.time().'.'.RandAlphaNum::handle(32));
    }

    /**
     * 生成微秒数.
     */
    protected function parseMicrotime(): string
    {
        [$usec, $sec] = explode(' ', microtime());

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
     * 闪存值 KEY.
     */
    protected function flashDataKey(string $key): string
    {
        return ISession::FLASH_DATA_KEY_PREFIX.$key;
    }
}
