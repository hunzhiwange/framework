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

namespace Leevel\Cache;

use InvalidArgumentException;

/**
 * 文件缓存.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.02.15
 *
 * @version 1.0
 */
class File extends Connect implements IConnect
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
    protected $option = [
        'time_preset' => [],
        'expire'      => 86400,
        'path'        => '',
        'serialize'   => true,
    ];

    /**
     * 获取缓存.
     *
     * @param string $name
     * @param mixed  $defaults
     * @param array  $option
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
            throw new InvalidArgumentException('Cache path is not readable.');
        }

        $fp = fopen($cachePath, 'rb');
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
     * @param string $name
     * @param mixed  $data
     * @param array  $option
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
     *
     * @param string $name
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
     *
     * @param string $name
     * @param array  $option
     *
     * @return bool
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
     * @param string $name
     *
     * @return string
     */
    protected function getCachePath(string $name): string
    {
        if (!$this->option['path']) {
            throw new InvalidArgumentException('Cache path is not allowed empty.');
        }

        return $this->option['path'].'/'.$this->getCacheName($name).'.php';
    }

    /**
     * 写入缓存数据.
     *
     * @param string $fileName
     * @param string $data
     */
    protected function writeData(string $fileName, string $data): void
    {
        $dirname = dirname($fileName);

        if (!is_dir($dirname)) {
            if (is_dir(dirname($dirname)) && !is_writable(dirname($dirname))) {
                throw new InvalidArgumentException(
                    sprintf('Unable to create the %s directory.', $dirname)
                );
            }

            mkdir($dirname, 0777, true);
        }

        if (!is_writable($dirname) ||
            !file_put_contents($fileName, $data, LOCK_EX)) {
            throw new InvalidArgumentException(
                sprintf('Dir %s is not writeable.', $dirname)
            );
        }

        chmod($fileName, 0666 & ~umask());
    }

    /**
     * 验证缓存是否存在.
     *
     * @param string $name
     *
     * @return bool
     */
    protected function exist(string $name): bool
    {
        return is_file($this->getCachePath($name));
    }

    /**
     * 获取缓存名字
     * 去掉特殊缓存名字字符.
     *
     * @param string $name
     *
     * @return string
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
