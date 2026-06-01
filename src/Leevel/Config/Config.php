<?php

declare(strict_types=1);

namespace Leevel\Config;

/**
 * 配置管理类.
 */
class Config implements IConfig, \ArrayAccess
{
    /**
     * 配置数据.
     */
    protected array $config = [];

    /**
     * 构造函数.
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function has(string $name = 'app\\'): bool
    {
        $name = $this->parseNamespace($name);
        $namespaces = $name[0];
        $name = $name[1];

        if ('*' === $name) {
            return isset($this->config[$namespaces]);
        }

        if (!strpos($name, '.')) {
            return \array_key_exists($name, $this->config[$namespaces]);
        }

        $config = $this->config[$namespaces];
        $parts = explode('.', $name);
        foreach ($parts as $part) {
            if (!isset($config[$part])) {
                return false;
            }
            $config = $config[$part];
        }

        return true;
    }

    public function get(string $name = 'app\\', mixed $defaults = null): mixed
    {
        $name = $this->parseNamespace($name);
        $namespaces = $name[0];
        $name = $name[1];

        if ('*' === $name) {
            return $this->config[$namespaces];
        }

        if (!strpos($name, '.')) {
            return \array_key_exists($name, $this->config[$namespaces])
                ? $this->config[$namespaces][$name]
                : $defaults;
        }

        $config = $this->config[$namespaces];
        $parts = explode('.', $name);
        foreach ($parts as $part) {
            if (!isset($config[$part])) {
                return $defaults;
            }
            $config = $config[$part];
        }

        return $config;
    }

    public function all(): array
    {
        return $this->config;
    }

    public function set(mixed $name, mixed $value = null): void
    {
        if (\is_array($name)) {
            foreach ($name as $key => $value) {
                $this->set($key, $value);
            }
        } else {
            $name = $this->parseNamespace($name);
            $namespaces = $name[0];
            $name = $name[1];

            if ('*' === $name) {
                $this->config[$namespaces] = $value;

                return;
            }

            if (!strpos($name, '.')) {
                $this->config[$namespaces][$name] = $value;
            } else {
                $parts = explode('.', $name);
                $max = \count($parts) - 1;
                $config = &$this->config[$namespaces];
                for ($i = 0; $i <= $max; ++$i) {
                    $part = $parts[$i];
                    if ($i < $max) {
                        if (!isset($config[$part])) {
                            $config[$part] = [];
                        }
                        $config = &$config[$part];
                    } else {
                        $config[$part] = $value;
                    }
                }
            }
        }
    }

    public function delete(string $name): void
    {
        $name = $this->parseNamespace($name);
        $namespaces = $name[0];
        $name = $name[1];

        if ('*' === $name) {
            $this->config[$namespaces] = [];

            return;
        }

        if (!strpos($name, '.')) {
            if (isset($this->config[$namespaces][$name])) {
                unset($this->config[$namespaces][$name]);
            }
        } else {
            $parts = explode('.', $name);
            $max = \count($parts) - 1;
            $config = &$this->config[$namespaces];
            for ($i = 0; $i <= $max; ++$i) {
                $part = $parts[$i];
                if ($i < $max) {
                    if (!isset($config[$part])) {
                        break;
                    }
                    $config = &$config[$part];
                } else {
                    if (isset($config[$part])) {
                        unset($config[$part]);
                    }
                }
            }
        }
    }

    public function reset(mixed $namespaces = null): void
    {
        if (\is_array($namespaces)) {
            $this->config = $namespaces;
        } elseif (\is_string($namespaces)) {
            $this->config[$namespaces] = [];
        } else {
            $this->config = [];
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->delete($offset);
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

        if (!isset($this->config[$namespaces])) {
            $this->config[$namespaces] = [];
        }

        return [$namespaces, $name];
    }
}
