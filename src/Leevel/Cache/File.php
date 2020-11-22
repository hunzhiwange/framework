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
        'time_preset' => [],
        'expire'      => 86400,
        'path'        => '',
    ];

    /**
     * 当前过期时间.
     */
    protected int $currentExpire;

    /**
     * 获取缓存.
     *
     * @throws \InvalidArgumentException
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
     * 设置缓存.
     */
    public function set(string $name, mixed $data, ?int $expire = null): void
    {
        $expire = $this->normalizeExpire($name, $expire);
        $data = json_encode([(int) $expire, $this->encodeData($data)], JSON_UNESCAPED_UNICODE);
        $data = sprintf(static::HEADER, '/* '.date('Y-m-d H:i:s').'  */').$data;
        $cachePath = $this->getCachePath($name);
        $this->writeData($cachePath, $data);
    }

    /**
     * 清除缓存.
     */
    public function delete(string $name): void
    {
        $cachePath = $this->getCachePath($name);
        if ($this->exist($name)) {
            unlink($cachePath);
        }
    }

    /**
     * 缓存是否存在.
     */
    public function has(string $name): bool
    {
        return false !== $this->get($name);
    }

    /**
     * 自增.
     *
     * @throws \InvalidArgumentException
     */
    public function increase(string $name, int $step = 1, ?int $expire = null): false|int
    {
        $data = $this->get($name, false);
        if (false === $data) {
            $expire = $this->normalizeExpire($name, $expire);
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
     * 自减.
     */
    public function decrease(string $name, int $step = 1, ?int $expire = null): false|int
    {
        return $this->increase($name, -$step, $expire);
    }

    /**
     * 获取缓存剩余时间.
     *
     * - 不存在的 key:-2
     * - key 存在，但没有设置剩余生存时间:-1
     * - 有剩余生存时间的 key:剩余时间
     */
    public function ttl(string $name): int
    {
        if (false === $this->get($name)) {
            return -2;
        }

        return $this->currentExpire <= 0 ? -1 : $this->currentExpire;
    }

    /**
     * 关闭.
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
        $expire = $this->cacheTime($name, $expire);
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
