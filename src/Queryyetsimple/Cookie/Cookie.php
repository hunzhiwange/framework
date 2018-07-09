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

namespace Leevel\Cookie;

use Exception;

/**
 * cookie 封装.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.19
 *
 * @version 1.0
 */
class Cookie implements ICookie
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'prefix'   => 'q_',
        'expire'   => 86400,
        'domain'   => '',
        'path'     => '/',
        'httponly' => false,
    ];

    /**
     * Cookie 设置数据.
     *
     * @var array
     */
    protected $cookies = [];

    /**
     * 构造函数.
     *
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setOption(string $name, $value): void
    {
        $this->option[$name] = $value;
    }

    /**
     * 设置 COOKIE.
     *
     * @param string $name
     * @param string $value
     * @param array  $option
     */
    public function set($name, $value = '', array $option = [])
    {
        $option = $this->normalizeOptions($option);

        if (is_array($value)) {
            $value = json_encode($value);
        }

        if (!is_scalar($value) && null !== $value) {
            throw new Exception('Cookie value must be scalar or null');
        }

        $name = $option['prefix'].$name;

        if ($option['expire'] > 0) {
            $option['expire'] = time() + $option['expire'];
        } elseif ($option['expire'] < 0) {
            $option['expire'] = time() - 31536000;
        } else {
            $option['expire'] = 0;
        }

        $isHttpSecure = false;
        if (!empty($_SERVER['HTTPS']) && 'ON' === strtoupper($_SERVER['HTTPS'])) {
            $isHttpSecure = true;
        }

        // 对应 setcookie 的参数
        $this->cookies[$name] = [
            $name,
            $value,
            $option['expire'],
            $option['path'],
            $option['domain'],
            $isHttpSecure,
            $option['httponly'],
        ];
    }

    /**
     * 批量插入.
     *
     * @param array|string $keys
     * @param mixed        $value
     * @param array        $option
     */
    public function put($keys, $value = null, array $option = [])
    {
        if (!is_array($keys)) {
            $keys = [
                $keys => $value,
            ];
        }

        foreach ($keys as $key => $value) {
            $this->set($key, $value, $option);
        }
    }

    /**
     * 数组插入数据.
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $option
     */
    public function push($key, $value, array $option = [])
    {
        $arr = $this->get($key, [], $option);
        $arr[] = $value;
        $this->set($key, $arr, $option);
    }

    /**
     * 合并元素.
     *
     * @param string $key
     * @param array  $value
     * @param array  $option
     */
    public function merge($key, array $value, array $option = [])
    {
        $this->set($key, array_unique(array_merge($this->get($key, [], $option), $value)), $option);
    }

    /**
     * 弹出元素.
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $option
     */
    public function pop($key, array $value, array $option = [])
    {
        $this->set($key, array_diff($this->get($key, [], $option), $value), $option);
    }

    /**
     * 数组插入键值对数据.
     *
     * @param string $key
     * @param mixed  $keys
     * @param mixed  $value
     * @param array  $option
     */
    public function arr($key, $keys, $value = null, array $option = [])
    {
        $arr = $this->get($key, [], $option);

        if (is_string($keys)) {
            $arr[$keys] = $value;
        } elseif (is_array($keys)) {
            $arr = array_merge($arr, $keys);
        }

        $this->set($key, $arr, $option);
    }

    /**
     * 数组键值删除数据.
     *
     * @param string $key
     * @param mixed  $keys
     */
    public function arrDelete($key, $keys, array $option = [])
    {
        $arr = $this->get($key, [], $option);

        if (!is_array($keys)) {
            $keys = [
                $keys,
            ];
        }

        foreach ($keys as $tempKey) {
            if (isset($arr[$tempKey])) {
                unset($arr[$tempKey]);
            }
        }

        $this->set($key, $arr, $option);
    }

    /**
     * 获取 cookie.
     *
     * @param string $name
     * @param mixed  $defaults
     * @param array  $option
     *
     * @return mixed
     */
    public function get($name, $defaults = null, array $option = [])
    {
        $option = $this->normalizeOptions($option);
        $name = $option['prefix'].$name;

        if (isset($this->cookies[$name])) {
            if ($this->isJson($this->cookies[$name])) {
                return json_decode($this->cookies[$name], true);
            }

            return $this->cookies[$name];
        }

        return $defaults;
    }

    /**
     * 删除 cookie.
     *
     * @param string $name
     * @param array  $option
     */
    public function delete($name, array $option = [])
    {
        $this->set($name, null, $option);
    }

    /**
     * 清空 cookie.
     *
     * @param bool  $deletePrefix
     * @param array $option
     */
    public function clear($deletePrefix = true, array $option = [])
    {
        $option = $this->normalizeOptions($option);
        $prefix = $option['prefix'];
        $option['prefix'] = '';

        foreach ($this->cookies as $key => $val) {
            if (true === $deletePrefix && $prefix) {
                if (0 === strpos($key, $prefix)) {
                    $this->delete($key, $option);
                }
            } else {
                $this->delete($key, $option);
            }
        }
    }

    /**
     * 返回所有 cookie.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->cookies;
    }

    /**
     * 整理配置.
     *
     * @param array $option
     *
     * @return array
     */
    protected function normalizeOptions(array $option = [])
    {
        return $option ? array_merge($this->option, $option) : $this->option;
    }

    /**
     * 验证是否为正常的 JSON 字符串.
     *
     * @param mixed $data
     *
     * @return bool
     */
    protected function isJson($data)
    {
        if (!is_scalar($data) && !method_exists($data, '__toString')) {
            return false;
        }

        json_decode($data);

        return JSON_ERROR_NONE === json_last_error();
    }
}
