<?php

declare(strict_types=1);

namespace Leevel\Cache;

use InvalidArgumentException;
use Leevel\Filesystem\Helper\create_file;
use function Leevel\Filesystem\Helper\create_file;

/**
 * 文件缓存.
 */
class File extends Cache implements ICache
{
    /**
     * 缓存文件头部.
    */
    const HEADER = '<?php die(%s); ?>';

    /**
     * 缓存文件头部长度.
     */
    const HEADER_LENGTH = 41;

    /**
     * 配置.
     */
    protected array $option = [
        'expire' => 86400,
        'path'   => '',
    ];

    /**
     * 当前过期时间.
     */
    protected int $currentExpire;

    /**
     * {@inheritDoc}
     */
    public function get(string $name, mixed $defaults = false): mixed
    {
        $data = $this->readFromFile($cachePath = $this->getCachePath($name));
        if (false === $data) {
            return false;
        }

        $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data) || !isset($data[0]) || !isset($data[1]) ||
            !is_int($data[0]) || !is_string($data[1])) {
            return false;
        }
        list($expire, $data) = $data;
        $this->currentExpire = $expire;
        if ($this->isExpired($name, $expire)) {
            unlink($cachePath);

            return false;
        }

        return $this->decodeData($data);
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $name, mixed $data, ?int $expire = null): void
    {
        $expire = $this->normalizeExpire($expire);
        $data = json_encode([(int) $expire, $this->encodeData($data)], JSON_UNESCAPED_UNICODE);
        $data = sprintf(static::HEADER, '/* '.date('Y-m-d H:i:s').'  */').$data;
        $cachePath = $this->getCachePath($name);
        $this->writeData($cachePath, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $name): void
    {
        $cachePath = $this->getCachePath($name);
        if ($this->exist($name)) {
            unlink($cachePath);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        return false !== $this->get($name);
    }

    /**
     * {@inheritDoc}
     */
    public function increase(string $name, int $step = 1, ?int $expire = null): false|int
    {
        $data = $this->get($name, false);
        if (false === $data) {
            $expire = $this->normalizeExpire($expire);
            $this->set($name, json_encode([$expire + time(), $step], JSON_THROW_ON_ERROR), $expire);

            return $step;
        }

        $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data) || !isset($data[0]) || !isset($data[1]) ||
            !is_int($data[0]) || !is_int($data[1])) {
            return false;
        }

        list($expireEndTime, $value) = $data;
        $expireEndTime = (int) $expireEndTime;
        $value += $step;
        $expire = $expireEndTime - time();
        if ($expire <= 1) {
            $expire = 1;
        }
        $this->set($name, json_encode([$expireEndTime, $value], JSON_THROW_ON_ERROR), $expire);

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function decrease(string $name, int $step = 1, ?int $expire = null): false|int
    {
        return $this->increase($name, -$step, $expire);
    }

    /**
     * {@inheritDoc}
     */
    public function ttl(string $name): int
    {
        if (false === $this->get($name)) {
            return -2;
        }

        return $this->currentExpire <= 0 ? -1 : $this->currentExpire;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
    }

    /**
     * 从文件读取内容.
     *
     * @throws \InvalidArgumentException
     */
    protected function readFromFile(string $cachePath): false|string
    {
        clearstatcache();

        if (!is_file($cachePath)) {
            return false;
        }

        if (!is_readable($cachePath)) {
            $e = 'Cache path is not readable.';

            throw new InvalidArgumentException($e);
        }

        $fp = fopen($cachePath, 'r');
        flock($fp, LOCK_SH);

        $len = filesize($cachePath);
        fread($fp, static::HEADER_LENGTH);
        $len -= static::HEADER_LENGTH;
        if ($len > 0) {
            $data = fread($fp, (int) $len);
        } else {
            $data = false;
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return $data;
    }

    /**
     * 验证缓存是否过期.
     */
    protected function isExpired(string $name, int $expire): bool
    {
        if ($expire <= 0) {
            return false;
        }

        return filemtime($this->getCachePath($name)) < time() - $expire;
    }

    /**
     * 获取缓存路径.
     *
     * @throws \InvalidArgumentException
     */
    protected function getCachePath(string $name): string
    {
        if (!$this->option['path']) {
            $e = 'Cache path is not allowed empty.';

            throw new InvalidArgumentException($e);
        }

        return $this->option['path'].'/'.$this->getCacheName($name).'.php';
    }

    /**
     * 写入缓存数据.
     */
    protected function writeData(string $fileName, string $data): void
    {
        create_file($fileName);
        file_put_contents($fileName, $data, LOCK_EX);
    }

    /**
     * 验证缓存是否存在.
     */
    protected function exist(string $name): bool
    {
        return is_file($this->getCachePath($name));
    }

    /**
     * 获取缓存名字.
     *
     * - 例外冒号字符 “:” 可作为目录分隔符，其在 Redis 起到类似目录的作用。
     */
    protected function getCacheName(string $name): string
    {
        return str_replace(':', \DIRECTORY_SEPARATOR, parent::getCacheName($name));
    }
}

// import fn.
class_exists(create_file::class);
