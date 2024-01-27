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

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    public function get(string $name = 'app\\', mixed $defaults = null): mixed
    {
        $name = $this->parseNamespace($name);
        $namespaces = $name[0];
        $name = $name[1];

        if ('*' === $name) {
            return $this->config[$namespaces];
        }

        if (!strpos($name, '.')) {
            return \array_key_exists($name, $this->config[$namespaces]) ?
                $this->config[$namespaces][$name] :
                $defaults;
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

    /**
     * {@inheritDoc}
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
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
