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
        'prefix' => '_',
        'expire' => 86400,
        'path' => '',
        'serialize' => true,
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
    public function get($name, $defaults = false, array $option = [])
    {
        $option = $this->normalizeOptions($option);
        $cachePath = $this->getCachePath($name, $option);

        // 清理文件状态缓存 http://php.net/manual/zh/function.clearstatcache.php
        clearstatcache();

        if (!is_file($cachePath)) {
            return false;
        }

        $fp = fopen($cachePath, 'rb');
        if (!$fp) {
            return false;
        }
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
    public function set($name, $data, array $option = [])
    {
        $option = $this->normalizeOptions($option);

        if ($option['serialize']) {
            $data = serialize($data);
        }
        $data = sprintf(static::HEADER, '/* '.date('Y-m-d H:i:s').'  */').$data;

        $cachePath = $this->getCachePath($name, $option);
        $this->writeData($cachePath, $data);
    }

    /**
     * 清除缓存.
     *
     * @param string $name
     * @param array  $option
     */
    public function delete($name, array $option = [])
    {
        $option = $this->normalizeOptions($option);

        $cachePath = $this->getCachePath($name, $option);

        if ($this->exist($name, $option)) {
            @unlink($cachePath);
        }
    }

    /**
     * 验证缓存是否过期
     *
     * @param string $name
     * @param array  $option
     *
     * @return bool
     */
    protected function isExpired($name, $option)
    {
        $filePath = $this->getCachePath($name, $option);

        if (!is_file($filePath)) {
            return true;
        }

        $option['expire'] = $this->cacheTime($name, $option['expire']);

        return (int) $option['expire'] > 0 && filemtime($filePath) + (int) $option['expire'] < time();
    }

    /**
     * 获取缓存路径.
     *
     * @param string $name
     * @param array  $option
     *
     * @return string
     */
    protected function getCachePath($name, $option)
    {
        if (!$option['path']) {
            throw new InvalidArgumentException('Cache path is not allowed empty.');
        }

        if (!is_dir($option['path'])) {
            mkdir($option['path'], 0777, true);
        }

        return $option['path'].'/'.$this->getCacheName($name, $option['prefix']).'.php';
    }

    /**
     * 写入缓存数据.
     *
     * @param string $fileName
     * @param string $data
     */
    protected function writeData($fileName, $data)
    {
        !is_dir(dirname($fileName)) && mkdir(dirname($fileName), 0777, true);

        if (!file_put_contents($fileName, $data, LOCK_EX)) {
            throw new InvalidArgumentException(sprintf('Dir %s is not writeable', dirname($fileName)));
        }

        chmod($fileName, 0777);
    }

    /**
     * 验证缓存是否存在.
     *
     * @param string $name
     * @param array  $option
     *
     * @return bool
     */
    protected function exist($name, $option)
    {
        return is_file($this->getCachePath($name, $option));
    }

    /**
     * 获取缓存名字
     * 去掉特殊缓存名字字符.
     *
     * @param string $name
     * @param string $prefix
     *
     * @return string
     */
    protected function getCacheName($name, $prefix = '')
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
        ], '.', parent::getCacheName($name, $prefix));
    }
}
