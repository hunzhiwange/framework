<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Cookie;

use Exception;
use Queryyetsimple\Option\TClass;

/**
 * cookie 封装
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
class Cookie implements ICookie
{
    use TClass;

    /**
     * 配置
     *
     * @var array
     */
    protected $option = [
        'prefix' => 'q_',
        'expire' => 86400,
        'domain' => '',
        'path' => '/',
        'httponly' => false
    ];

    /**
     * 构造函数
     *
     * @param array $option
     * @return void
     */
    public function __construct(array $option = [])
    {
        $this->options($option);
    }

    /**
     * 设置 COOKIE
     *
     * @param string $name
     * @param string $value
     * @param array $option
     * @return void
     */
    public function set($name, $value = '', array $option = [])
    {
        $option = $this->getOptions($option);

        if (is_array($value)) {
            $value = json_encode($value);
        }

        if (! is_scalar($value) && ! is_null($value)) {
            throw new Exception('Cookie value must be scalar or null');
        }

        $name = $option['prefix'] . $name;

        if ($value === null || $option['expire'] < 0) {
            if (isset($_COOKIE[$name])) {
                unset($_COOKIE[$name]);
            }
        } else {
            $_COOKIE[$name] = $value;
        }

        if ($option["expire"] > 0) {
            $option["expire"] = time() + $option["expire"];
        } elseif ($option["expire"] < 0) {
            $option["expire"] = time() - 31536000;
        } else {
            $option["expire"] = 0;
        }

        $isHttpSecure = false;
        if (! empty($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS']) === 'ON') {
            $isHttpSecure = true;
        }

        setcookie($name, $value, $option['expire'], $option['path'], $option['domain'], $isHttpSecure, $option['httponly']);
    }

    /**
     * 批量插入
     *
     * @param string|array $keys
     * @param mixed $value
     * @param array $option
     * @return void
     */
    public function put($keys, $value = null, array $option = [])
    {
        if (! is_array($keys)) {
            $keys = [
                $keys => $value
            ];
        }

        foreach ($keys as $key => $value) {
            $this->set($key, $value, $option);
        }
    }

    /**
     * 数组插入数据
     *
     * @param string $key
     * @param mixed $value
     * @param array $option
     * @return void
     */
    public function push($key, $value, array $option = [])
    {
        $arr = $this->get($key, [], $option);
        $arr[] = $value;
        $this->set($key, $arr, $option);
    }

    /**
     * 合并元素
     *
     * @param string $key
     * @param array $value
     * @param array $option
     * @return void
     */
    public function merge($key, array $value, array $option = [])
    {
        $this->set($key, array_unique(array_merge($this->get($key, [], $option), $value)), $option);
    }

    /**
     * 弹出元素
     *
     * @param string $key
     * @param mixed $value
     * @param array $option
     * @return void
     */
    public function pop($key, array $value, array $option = [])
    {
        $this->set($key, array_diff($this->get($key, [], $option), $value), $option);
    }

    /**
     * 数组插入键值对数据
     *
     * @param string $key
     * @param mixed $keys
     * @param mixed $value
     * @param array $option
     * @return void
     */
    public function arrays($key, $keys, $value = null, array $option = [])
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
     * 数组键值删除数据
     *
     * @param string $key
     * @param mixed $keys
     * @return void
     */
    public function arraysDelete($key, $keys, array $option = [])
    {
        $arr = $this->get($key, [], $option);

        if (! is_array($keys)) {
            $keys = [
                $keys
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
     * 获取 cookie
     *
     * @param string $name
     * @param mixed $defaults
     * @param array $option
     * @return mixed
     */
    public function get($name, $defaults = null, array $option = [])
    {
        $option = $this->getOptions($option);
        $name = $option['prefix'] . $name;

        if (isset($_COOKIE[$name])) {
            if ($this->isJson($_COOKIE[$name])) {
                return json_decode($_COOKIE[$name], true);
            }
            return $_COOKIE[$name];
        } else {
            return $defaults;
        }
    }

    /**
     * 删除 cookie
     *
     * @param string $name
     * @param array $option
     * @return void
     */
    public function delete($name, array $option = [])
    {
        $this->set($name, null, $option);
    }

    /**
     * 清空 cookie
     *
     * @param boolean $deletePrefix
     * @param array $option
     * @return void
     */
    public function clear($deletePrefix = true, array $option = [])
    {
        $option = $this->getOptions($option);
        $prefix = $option['prefix'];
        $option['prefix'] = '';

        foreach ($_COOKIE as $key => $val) {
            if ($deletePrefix === true && $prefix) {
                if (strpos($key, $prefix) === 0) {
                    $this->delete($key, $option);
                }
            } else {
                $this->delete($key, $option);
            }
        }
    }

    /**
     * 验证是否为正常的 JSON 字符串
     *
     * @param mixed $data
     * @return boolean
     */
    protected function isJson($data)
    {
        if (! is_scalar($data) && ! method_exists($data, '__toString')) {
            return false;
        }

        json_decode($data);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
