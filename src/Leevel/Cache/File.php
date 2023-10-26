<?php

declare(strict_types=1);

namespace Leevel\Cache;

use Leevel\Filesystem\Helper\CreateFile;

/**
 * 文件缓存.
 */
class File extends Cache implements ICache
{
    /**
     * 缓存文件头部.
     */
    public const HEADER = '<?php die(%s); ?>';

    /**
     * 缓存文件头部长度.
     */
    public const HEADER_LENGTH = 41;

    /**
     * 配置.
     */
    protected array $option = [
        'expire' => 86400,
        'path' => '',
    ];

    /**
     * 当前过期时间.
     */
    protected int $currentExpire = 0;

    /**
     * {@inheritDoc}
     */
    public function get(string $name, mixed $defaults = false): mixed
    {
        clearstatcache();
        $data = $this->readFromFile($cachePath = $this->getCachePath($name));
        if (false === $data) {
            return false;
        }

        $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        if (!\is_array($data) || !isset($data[0]) || !isset($data[1])
            || !\is_int($data[0]) || !\is_string($data[1])) {
            return false;
        }

        [$expire, $data] = $data;
        $this->currentExpire = $expire;
        if ($this->isExpired($name, $expire)) {
            // 过期不删除缓存文件，并发情况下会有文件写入此文件
            return false;
        }

        return $this->decodeData($data);
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $name, mixed $data, ?int $expire = null): void
    {
        clearstatcache();
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
        clearstatcache();
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
     *
     * @throws \JsonException
     */
    public function increase(string $name, int $step = 1, ?int $expire = null): false|int
    {
        $data = $this->get($name);
        if (false === $data) {
            $expire = $this->normalizeExpire($expire);
            $this->set($name, json_encode([$expire + time(), $step], JSON_THROW_ON_ERROR), $expire);

            return $step;
        }

        $data = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        if (!\is_array($data) || !isset($data[0]) || !isset($data[1])
            || !\is_int($data[0]) || !\is_int($data[1])) {
            return false;
        }

        [$expireEndTime, $value] = $data;
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
     * @throws \Exception
     */
    protected function readFromFile(string $cachePath): false|string
    {
        if (!is_file($cachePath)) {
            return false;
        }

        if (!is_readable($cachePath)) {
            throw new \InvalidArgumentException('Cache path is not readable.');
        }

        $fp = fopen($cachePath, 'r');
        if (false === $fp) {
            throw new \Exception(sprintf('Open file %s failed.', $cachePath)); // @codeCoverageIgnore
        }

        $lockResult = flock($fp, LOCK_SH);
        if (false === $lockResult) {
            throw new \Exception(sprintf('Lock file %s failed.', $cachePath)); // @codeCoverageIgnore
        }

        $len = filesize($cachePath);
        if (false === $len) {
            throw new \Exception(sprintf('Get file size of file %s failed.', $cachePath)); // @codeCoverageIgnore
        }

        $readResult = fread($fp, static::HEADER_LENGTH);
        if (false === $readResult) {
            throw new \Exception(sprintf('Read file %s failed.', $cachePath)); // @codeCoverageIgnore
        }

        $len -= static::HEADER_LENGTH;
        if ($len > 0) {
            $data = fread($fp, $len);
            if (false === $data) {
                throw new \Exception(sprintf('Read file %s failed.', $cachePath)); // @codeCoverageIgnore
            }
        } else {
            $data = false;
        }

        $unlockResult = flock($fp, LOCK_UN);
        if (false === $unlockResult) {
            throw new \Exception(sprintf('Unlock file %s failed.', $cachePath)); // @codeCoverageIgnore
        }

        $closeResult = fclose($fp);
        if (false === $closeResult) {
            throw new \Exception(sprintf('Close file %s failed.', $cachePath)); // @codeCoverageIgnore
        }

        return $data;
    }

    /**
     * 验证缓存是否过期.
     *
     * @throws \Exception
     */
    protected function isExpired(string $name, int $expire): bool
    {
        if ($expire <= 0) {
            return false;
        }

        $fileTime = filemtime($cachePath = $this->getCachePath($name));
        if (false === $fileTime) {
            throw new \Exception(sprintf('Get file modification time of file %s failed.', $cachePath)); // @codeCoverageIgnore
        }

        return $fileTime < time() - $expire;
    }

    /**
     * 获取缓存路径.
     *
     * @throws \InvalidArgumentException
     */
    protected function getCachePath(string $name): string
    {
        if (!$this->option['path']) {
            throw new \InvalidArgumentException('Cache path is not allowed empty.');
        }

        return $this->option['path'].'/'.$this->getCacheName($name).'.php';
    }

    /**
     * 写入缓存数据.
     *
     * @throws \Exception
     */
    protected function writeData(string $fileName, string $data): void
    {
        CreateFile::handle($fileName);
        $result = file_put_contents($fileName, $data, LOCK_EX);
        if (false === $result) {
            throw new \Exception(sprintf('Write file %s failed.', $fileName)); // @codeCoverageIgnore
        }
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
