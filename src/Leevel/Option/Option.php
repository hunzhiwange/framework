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

namespace Leevel\Option;

use ArrayAccess;

/**
 * 配置管理类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.02.13
 *
 * @version 1.0
 */
class Option implements IOption, ArrayAccess
{
    /**
     * 配置数据.
     *
     * @var array
     */
    protected array $option = [];

    /**
     * 构造函数.
     *
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        $this->option = $option;
    }

    /**
     * 是否存在配置.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name = 'app\\'): bool
    {
        $name = $this->parseNamespace($name);
        $namespaces = $name[0];
        $name = $name[1];

        if ('*' === $name) {
            return isset($this->option[$namespaces]);
        }

        if (!strpos($name, '.')) {
            return array_key_exists($name, $this->option[$namespaces]);
        }

        $parts = explode('.', $name);
        $option = $this->option[$namespaces];

        foreach ($parts as $part) {
            if (!isset($option[$part])) {
                return false;
            }

            $option = $option[$part];
        }

        return true;
    }

    /**
     * 获取配置.
     *
     * @param string     $name
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public function get(string $name = 'app\\', $defaults = null)
    {
        $name = $this->parseNamespace($name);
        $namespaces = $name[0];
        $name = $name[1];

        if ('*' === $name) {
            return $this->option[$namespaces];
        }

        if (!strpos($name, '.')) {
            return array_key_exists($name, $this->option[$namespaces]) ?
                $this->option[$namespaces][$name] :
                $defaults;
        }

        $parts = explode('.', $name);
        $option = $this->option[$namespaces];

        foreach ($parts as $part) {
            if (!isset($option[$part])) {
                return $defaults;
            }

            $option = $option[$part];
        }

        return $option;
    }

    /**
     * 返回所有配置.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->option;
    }

    /**
     * 设置配置.
     *
     * @param mixed      $name
     * @param null|mixed $value
     */
    public function set($name, $value = null): void
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->set($key, $value);
            }
        } else {
            $name = $this->parseNamespace($name);
            $namespaces = $name[0];
            $name = $name[1];

            if ('*' === $name) {
                $this->option[$namespaces] = $value;

                return;
            }

            if (!strpos($name, '.')) {
                $this->option[$namespaces][$name] = $value;
            } else {
                $parts = explode('.', $name);
                $max = count($parts) - 1;
                $option = &$this->option[$namespaces];

                for ($i = 0; $i <= $max; $i++) {
                    $part = $parts[$i];

                    if ($i < $max) {
                        if (!isset($option[$part])) {
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
     * 删除配置.
     *
     * @param string $name
     */
    public function delete(string $name): void
    {
        $name = $this->parseNamespace($name);
        $namespaces = $name[0];
        $name = $name[1];

        if ('*' === $name) {
            $this->option[$namespaces] = [];

            return;
        }

        if (!strpos($name, '.')) {
            if (isset($this->option[$namespaces][$name])) {
                unset($this->option[$namespaces][$name]);
            }
        } else {
            $parts = explode('.', $name);
            $max = count($parts) - 1;
            $option = &$this->option[$namespaces];

            for ($i = 0; $i <= $max; $i++) {
                $part = $parts[$i];

                if ($i < $max) {
                    if (!isset($option[$part])) {
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
     * 初始化配置参数.
     *
     * @param null|mixed $namespaces
     */
    public function reset($namespaces = null): void
    {
        if (is_array($namespaces)) {
            $this->option = $namespaces;
        } elseif (is_string($namespaces)) {
            $this->option[$namespaces] = [];
        } else {
            $this->option = [];
        }
    }

    /**
     * 实现 ArrayAccess::offsetExists.
     *
     * @param mixed $index
     *
     * @return bool
     */
    public function offsetExists($index): bool
    {
        return $this->has($index);
    }

    /**
     * 实现 ArrayAccess::offsetGet.
     *
     * @param mixed $index
     *
     * @return mixed
     */
    public function offsetGet($index)
    {
        return $this->get($index);
    }

    /**
     * 实现 ArrayAccess::offsetSet.
     *
     * @param mixed $index
     * @param mixed $newval
     */
    public function offsetSet($index, $newval): void
    {
        $this->set($index, $newval);
    }

    /**
     * 实现 ArrayAccess::offsetUnset.
     *
     * @param string $index
     */
    public function offsetUnset($index): void
    {
        $this->delete($index);
    }

    /**
     * 分析命名空间.
     *
     * @param string $name
     *
     * @return array
     */
    protected function parseNamespace(string $name): array
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

        if (!isset($this->option[$namespaces])) {
            $this->option[$namespaces] = [];
        }

        return [
            $namespaces,
            $name,
        ];
    }
}
