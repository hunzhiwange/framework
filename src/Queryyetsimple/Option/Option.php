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
namespace Queryyetsimple\Option;

use ArrayAccess;

/**
 * 配置管理类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.02.13
 * @version 1.0
 */
class Option implements IOption, ArrayAccess
{

    /**
     * 配置数据
     *
     * @var array
     */
    protected $option = [];

    /**
     * 默认命名空间
     *
     * @var string
     */
    const DEFAUTL_NAMESPACE = 'app';

    /**
     * 构造函数
     *
     * @param array $option
     * @return void
     */
    public function __construct(array $option = [])
    {
        $this->option = $option;
    }

    /**
     * 是否存在配置
     *
     * @param string $name 配置键值
     * @return string
     */
    public function has($name = 'app\\')
    {
        $name = $this->parseNamespace($name);
        $namespaces = $name[0];
        $name = $name[1];

        if ($name == '*') {
            return isset($this->option[$namespaces]);
        }

        if (! strpos($name, '.')) {
            return array_key_exists($name, $this->option[$namespaces]);
        }

        $parts = explode('.', $name);
        $option = $this->option[$namespaces];
        foreach ($parts as $part) {
            if (! isset($option[$part])) {
                return false;
            }
            $option = $option[$part];
        }

        return true;
    }

    /**
     * 获取配置
     *
     * @param string $name 配置键值
     * @param mixed $defaults 配置默认值
     * @return string
     */
    public function get($name = 'app\\', $defaults = null)
    {
        $name = $this->parseNamespace($name);
        $namespaces = $name[0];
        $name = $name[1];

        if ($name == '*') {
            return $this->option[$namespaces];
        }

        if (! strpos($name, '.')) {
            return array_key_exists($name, $this->option[$namespaces]) ? $this->option[$namespaces][$name] : $defaults;
        }

        $parts = explode('.', $name);
        $option = $this->option[$namespaces];
        foreach ($parts as $part) {
            if (! isset($option[$part])) {
                return $defaults;
            }
            $option = $option[$part];
        }

        return $option;
    }

    /**
     * 返回所有配置
     *
     * @return array
     */
    public function all()
    {
        return $this->option;
    }

    /**
     * 设置配置
     *
     * @param mixed $name 配置键值
     * @param mixed $value 配置值
     * @return array
     */
    public function set($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->set($key, $value);
            }
        } else {
            $name = $this->parseNamespace($name);
            $namespaces = $name[0];
            $name = $name[1];

            if ($name == '*') {
                $this->option[$namespaces] = $value;
                return;
            }

            if (! strpos($name, '.')) {
                $this->option[$namespaces][$name] = $value;
            } else {
                $parts = explode('.', $name);
                $max = count($parts) - 1;
                $option = &$this->option[$namespaces];
                for ($i = 0; $i <= $max; $i ++) {
                    $part = $parts[$i];
                    if ($i < $max) {
                        if (! isset($option[$part])) {
                            $option[$part] = [];
                        }
                        $option = &$option[$part];
                    } else {
                        $option[$part] = $value;
                    }
                }
            }
        }
    }

    /**
     * 删除配置
     *
     * @param string $name 配置键值
     * @return string
     */
    public function delete($name)
    {
        $name = $this->parseNamespace($name);
        $namespaces = $name[0];
        $name = $name[1];

        if ($name == '*') {
            $this->option[$namespaces] = [];
            return;
        }

        if (! strpos($name, '.')) {
            if (isset($this->option[$namespaces][$name])) {
                unset($this->option[$namespaces][$name]);
            }
        } else {
            $parts = explode('.', $name);
            $max = count($parts) - 1;
            $option = &$this->option[$namespaces];
            for ($i = 0; $i <= $max; $i ++) {
                $part = $parts[$i];
                if ($i < $max) {
                    if (! isset($option[$part])) {
                        break;
                    }
                    $option = &$option[$part];
                } else {
                    if (isset($option[$part])) {
                        unset($option[$part]);
                    }
                }
            }
        }
    }

    /**
     * 初始化配置参数
     *
     * @param mixed $namespaces
     * @return boolean
     */
    public function reset($namespaces = null)
    {
        if (is_array($namespaces)) {
            $this->option = $namespaces;
        } elseif (is_string($namespaces)) {
            if (isset($this->option[$namespaces])) {
                $this->option[$namespaces] = [];
            }
        } else {
            $this->option = [];
        }

        return true;
    }

    /**
     * 判断配置是否存在
     *
     * @param string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return $this->has($name);
    }

    /**
     * 获取配置
     *
     * @param string $name
     * @return mixed
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * 设置配置
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function offsetSet($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * 删除配置
     *
     * @param string $name
     * @return void
     */
    public function offsetUnset($name)
    {
        $this->delete($name);
    }

    /**
     * 分析命名空间
     *
     * @param string $name
     * @return array
     */
    protected function parseNamespace($name)
    {
        if (strpos($name, '\\')) {
            $namespaces = explode('\\', $name);
            if (empty($namespaces[1])) {
                $namespaces[1] = '*';
            }
            $name = $namespaces[1];
            $namespaces = $namespaces[0];
        } else {
            $namespaces = static::DEFAUTL_NAMESPACE;
        }

        if (! isset($this->option[$namespaces])) {
            $this->option[$namespaces] = [];
        }

        return [
            $namespaces,
            $name
        ];
    }
}
