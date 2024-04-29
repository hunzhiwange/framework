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
    protected array $config = [];

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
        $this->setName($this->config['name']);
    }

    public function start(?string $sessionId = null): void
    {
        if ($this->isStart()) {
            return;
        }

        $this->setId($sessionId ?: $this->config['id']);
        $this->loadData();
        $this->started = true;
    }

    /**
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

    public function setExpire(?int $expire = null): void
    {
        $this->expire = $expire;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function set(string $name, mixed $value): void
    {
        $name = $this->getNormalizeName($name);
        $this->data[$name] = $value;
    }

    public function put(array|string $keys, mixed $value = null): void
    {
        if (!\is_array($keys)) {
            $keys = [$keys => $value];
        }

        foreach ($keys as $item => $value) {
            $this->set($item, $value);
        }
    }

    public function get(string $name, mixed $defaults = null): mixed
    {
        $name = $this->getNormalizeName($name);

        return $this->data[$name] ?? $defaults;
    }

    public function delete(string $name): void
    {
        $name = $this->getNormalizeName($name);
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
    }

    public function has(string $name): bool
    {
        $name = $this->getNormalizeName($name);

        return isset($this->data[$name]);
    }

    public function clear(): void
    {
        $this->data = [];
    }

    public function flash(string $key, mixed $value): void
    {
        $this->set($this->flashDataKey($key), $value);
        $this->mergeNewFlash([$key]);
        $this->popOldFlash([$key]);
    }

    public function flashs(array $flash): void
    {
        foreach ($flash as $key => $value) {
            $this->flash($key, $value);
        }
    }

    public function nowFlash(string $key, mixed $value): void
    {
        $this->set($this->flashDataKey($key), $value);
        $this->mergeOldFlash([$key]);
    }

    public function nowFlashs(array $flash): void
    {
        foreach ($flash as $key => $value) {
            $this->nowFlash($key, $value);
        }
    }

    public function rebuildFlash(): void
    {
        $this->mergeNewFlash(
            $this->get(ISession::FLASH_OLD_KEY, [])
        );
        $this->set(ISession::FLASH_OLD_KEY, []);
    }

    public function keepFlash(array $keys): void
    {
        $this->mergeNewFlash($keys);
        $this->popOldFlash($keys);
    }

    public function getFlash(string $key, mixed $defaults = null): mixed
    {
        return $this->get($this->flashDataKey($key), $defaults);
    }

    public function deleteFlash(array $keys): void
    {
        foreach ($keys as $item) {
            $this->delete($this->flashDataKey($item));
        }
        $this->mergeOldFlash($keys);
        $this->popNewFlash($keys);
    }

    public function clearFlash(): void
    {
        $this->deleteFlash($this->get(ISession::FLASH_NEW_KEY, []));
    }

    public function unregisterFlash(): void
    {
        $data = $this->get(ISession::FLASH_NEW_KEY, []);
        $old = (array) $this->get(ISession::FLASH_OLD_KEY, []);
        foreach ($old as $item) {
            $this->delete($this->flashDataKey($item));
        }
        $this->delete(ISession::FLASH_NEW_KEY);
        $this->set(ISession::FLASH_OLD_KEY, $data);
    }

    public function prevUrl(): ?string
    {
        // @phpstan-ignore-next-line
        return $this->get(ISession::PREV_URL_KEY);
    }

    public function setPrevUrl(string $url): void
    {
        $this->set(ISession::PREV_URL_KEY, $url);
    }

    public function destroySession(): void
    {
        $this->clear();
        $this->destroy($this->getId());
        $this->id = '';
        $this->started = false;
    }

    public function isStart(): bool
    {
        return $this->started;
    }

    public function setName(?string $name = null): void
    {
        $this->name = $name ?: ISession::SESSION_NAME;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setId(?string $id = null): void
    {
        $this->id = $id ?: $this->generateSessionId();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function regenerateId(): string
    {
        return $this->id = $this->generateSessionId();
    }

    public function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $sessionId): string
    {
        $cacheData = $this->cache->get($this->getSessionName($sessionId), []);

        return serialize($cacheData ?: []);
    }

    public function write(string $sessionId, string $sessionData): bool
    {
        $this->cache->set(
            $this->getSessionName($sessionId),
            unserialize($sessionData),
            $this->expire
        );

        return true;
    }

    public function destroy(string $sessionId): bool
    {
        $this->cache->delete(
            $this->getSessionName($sessionId)
        );

        return true;
    }

    public function gc(int $maxLifetime): int
    {
        return 0;
    }

    /**
     * 合并元素.
     */
    protected function mergeItem(string $key, array $value): void
    {
        $this->set($key, array_merge((array) $this->get($key, []), $value));
    }

    /**
     * 弹出元素.
     */
    protected function popItem(string $key, array $value): void
    {
        $this->set($key, array_diff((array) $this->get($key, []), $value));
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
        return (array) unserialize($this->read($this->getId()));
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
