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
use Leevel\Filesystem\Fso\create_file;
use function Leevel\Filesystem\Fso\create_file;

/**
 * 文件缓存.
 */
class File extends Cache implements ICache
{
    /**
     * 缓存文件头部.
     *
     * @var string
     */
    const HEADER = '<?php die(%s); ?>';

    /**
     * 缓存文件头部长度.
     *
     * @var int
     */
    const HEADER_LENGTH = 41;

    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
        'time_preset' => [],
        'expire'      => 86400,
        'path'        => '',
        'serialize'   => true,
    ];

    /**
     * 获取缓存.
     *
     * @param mixed $defaults
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function get(string $name, $defaults = false, array $option = [])
    {
        $option = $this->normalizeOptions($option);
        $cachePath = $this->getCachePath($name);

        // 清理文件状态缓存 http://php.net/manual/zh/function.clearstatcache.php
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

        // 头部的 41 个字节存储了安全代码
        $len = filesize($cachePath);
        fread($fp, static::HEADER_LENGTH);
        $len -= static::HEADER_LENGTH;

        do {
            // 检查缓存是否已经过期
            if ($this->isExpired($name, $option)) {
                $data = false;

                break;
            }

            if ($len > 0) {
                $data = fread($fp, $len);
            } else {
                $data = false;
            }
        } while (false);

        flock($fp, LOCK_UN);
        fclose($fp);

        if (false === $data) {
            return false;
        }

        // 解码
        if ($option['serialize']) {
            $data = unserialize($data);
        }

        return $data;
    }

    /**
     * 设置缓存.
     *
     * @param mixed $data
     */
    public function set(string $name, $data, array $option = []): void
    {
        $option = $this->normalizeOptions($option);
        if ($option['serialize']) {
            $data = serialize($data);
        }

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
     * 关闭.
     */
    public function close(): void
    {
    }

    /**
     * 验证缓存是否过期
     */
    protected function isExpired(string $name, array $option): bool
    {
        $filePath = $this->getCachePath($name);
        $option['expire'] = $this->cacheTime($name, (int) $option['expire']);
        if ($option['expire'] <= 0) {
            return true;
        }

        return filemtime($filePath) + (int) $option['expire'] < time();
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
     * 获取缓存名字
     * 去掉特殊缓存名字字符.
     */
    protected function getCacheName(string $name): string
    {
        return str_replace([
            '?',
            '*',
            ':',
            '"',
            '<',
            '>',
            '\\',
            '/',
            '|',
        ], '.', parent::getCacheName($name));
    }
}

// import fn.
class_exists(create_file::class);
