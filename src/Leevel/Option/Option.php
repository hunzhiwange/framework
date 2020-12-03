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

namespace Leevel\Option;

use ArrayAccess;

/**
 * 配置管理类.
 */
class Option implements IOption, ArrayAccess
{
    /**
     * 配置数据.
     */
    protected array $option = [];

    /**
     * 构造函数.
     */
    public function __construct(array $option = [])
    {
        $this->option = $option;
    }

    /**
     * {@inheritDoc}
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

        $option = $this->option[$namespaces];
        $parts = explode('.', $name);
        foreach ($parts as $part) {
            if (!isset($option[$part])) {
                return false;
            }
            $option = $option[$part];
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name = 'app\\', mixed $defaults = null): mixed
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

        $option = $this->option[$namespaces];
        $parts = explode('.', $name);
        foreach ($parts as $part) {
            if (!isset($option[$part])) {
                return $defaults;
            }
            $option = $option[$part];
        }

        return $option;
    }

    /**
     * {@inheritDoc}
     */
    public function all(): array
    {
        return $this->option;
    }

    /**
     * {@inheritDoc}
     */
    public function set(mixed $name, mixed $value = null): void
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function reset(mixed $namespaces = null): void
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
     * {@inheritDoc}
     */
    public function offsetExists(mixed $index): bool
    {
        return $this->has($index);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet(mixed $index): mixed
    {
        return $this->get($index);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet(mixed $index, mixed $newval): void
    {
        $this->set($index, $newval);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset(mixed $index): void
    {
        $this->delete($index);
    }

    /**
     * 分析命名空间.
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

        return [$namespaces, $name];
    }
}
