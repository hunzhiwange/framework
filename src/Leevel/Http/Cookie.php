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

namespace Leevel\Http;

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
class Cookie
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'expire'   => 86400,
        'domain'   => '',
        'path'     => '/',
        'secure'   => false,
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
     *
     * @return $this
     */
    public function setOption(string $name, $value): self
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 设置 COOKIE.
     *
     * @param string            $name
     * @param null|array|string $value
     * @param array             $option
     */
    public function set(string $name, $value = null, array $option = []): void
    {
        $option = $this->normalizeOptions($option);

        if (is_array($value)) {
            $value = json_encode($value);
        } elseif (!is_string($value) && null !== $value) {
            throw new Exception('Cookie value must be string,array or null.');
        }

        $option['expire'] = (int) ($option['expire']);

        if ($option['expire'] < 0) {
            throw new Exception('Cookie expire date must greater than or equal 0.');
        }

        if ($option['expire'] > 0) {
            $option['expire'] = time() + $option['expire'];
        }

        // 对应 setcookie 的参数
        $this->cookies[$name] = [
            $name,
            $value,
            $option['expire'],
            $option['path'],
            $option['domain'],
            $option['secure'],
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
    public function put($keys, $value = null, array $option = []): void
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
    public function push(string $key, $value, array $option = []): void
    {
        $data = $this->get($key, [], $option);
        $data[] = $value;
        $this->set($key, $data, $option);
    }

    /**
     * 合并元素.
     *
     * @param string $key
     * @param array  $value
     * @param array  $option
     */
    public function merge(string $key, array $value, array $option = []): void
    {
        $this->set($key, array_merge($this->get($key, [], $option), $value), $option);
    }

    /**
     * 弹出元素.
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $option
     */
    public function pop(string $key, array $value, array $option = []): void
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
    public function arr(string $key, $keys, $value = null, array $option = []): void
    {
        $data = $this->get($key, [], $option);

        if (is_string($keys)) {
            $data[$keys] = $value;
        } elseif (is_array($keys)) {
            $data = array_merge($data, $keys);
        }

        $this->set($key, $data, $option);
    }

    /**
     * 数组键值删除数据.
     *
     * @param string $key
     * @param mixed  $keys
     */
    public function arrDelete(string $key, $keys, array $option = []): void
    {
        $data = $this->get($key, [], $option);

        if (!is_array($keys)) {
            $keys = [$keys];
        }

        foreach ($keys as $tmp) {
            if (isset($data[$tmp])) {
                unset($data[$tmp]);
            }
        }

        $this->set($key, $data, $option);
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
    public function get(string $name, $defaults = null, array $option = [])
    {
        $option = $this->normalizeOptions($option);

        if (isset($this->cookies[$name])) {
            if ($this->isJson($this->cookies[$name][1])) {
                return json_decode($this->cookies[$name][1], true);
            }

            return $this->cookies[$name][1];
        }

        return $defaults;
    }

    /**
     * 删除 cookie.
     *
     * @param string $name
     * @param array  $option
     */
    public function delete(string $name, array $option = []): void
    {
        $this->set($name, null, $option);
    }

    /**
     * 清空 cookie.
     *
     * @param array $option
     */
    public function clear(array $option = []): void
    {
        $option = $this->normalizeOptions($option);

        foreach ($this->cookies as $key => $val) {
            $this->delete($key, $option);
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
     * 格式化 COOKIE 为字符串.
     *
     * @param array $cookie
     *
     * @return string
     *
     * @see \Symfony\Component\HttpFoundation\Cookie::__string()
     */
    public static function format(array $cookie): string
    {
        if (7 !== count($cookie)) {
            throw new Exception('Invalid cookie data.');
        }

        $str = $cookie[0].'=';

        if ('' === (string) $cookie[1]) {
            $str .= 'deleted; expires='.gmdate('D, d-M-Y H:i:s T', time() - 31536001).'; Max-Age=0';
        } else {
            $str .= $cookie[1];

            if (0 !== $cookie[2]) {
                $str .= '; expires='.gmdate('D, d-M-Y H:i:s T', $cookie[2]).'; Max-Age='.
                    ($cookie[2] - time() ?: 0);
            }
        }

        if ($cookie[3]) {
            $str .= '; path='.$cookie[3];
        }

        if ($cookie[4]) {
            $str .= '; domain='.$cookie[4];
        }

        if (true === $cookie[5]) {
            $str .= '; secure';
        }

        if (true === $cookie[6]) {
            $str .= '; httponly';
        }

        return $str;
    }

    /**
     * 整理配置.
     *
     * @param array $option
     *
     * @return array
     */
    protected function normalizeOptions(array $option = []): array
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
    protected function isJson($data): bool
    {
        if (!is_scalar($data) && !method_exists($data, '__toString')) {
            return false;
        }

        json_decode((string) ($data));

        return JSON_ERROR_NONE === json_last_error();
    }
}
