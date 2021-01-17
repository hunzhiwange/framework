<?php

declare(strict_types=1);

namespace Leevel\Support;

use ArrayAccess;
use Leevel\Support\IArray;
use function Leevel\Support\Str\camelize;
use Leevel\Support\Str\camelize;
use function Leevel\Support\Str\un_camelize;
use Leevel\Support\Str\un_camelize;
use function Leevel\Support\Arr\only;
use Leevel\Support\Arr\only;
use function Leevel\Support\Arr\except;
use Leevel\Support\Arr\except;
use ReflectionClass;
use ReflectionProperty;
use UnexpectedValueException;

/**
 * 数据传输对象.
 */
abstract class Dto implements IArray, ArrayAccess
{
    /**
     * 驼峰法命名属性缓存.
     */
    protected static array $camelizePropertyNameCached = [];

    /**
     * 下划线命名属性缓存.
     */
    protected static array $unCamelizePropertyNameCached = [];

    /**
     * 类属性数据缓存.
     */
    protected static array $propertysCached = [];

    /**
     * 忽略丢失的值.
     */
    protected bool $ignoreMissingValues = true;

    /**
     * 忽略 NULL 值.
     */
    protected bool $ignoreNullValue = true;

    /**
     * 下划线命名风格.
     */
    protected bool $unCamelizeNamingStyle = true;

    /**
     * 黑名单属性.
     */
    protected array $excepPropertys = [];

    /**
     * 白名单属性.
     */
    protected array $onlyPropertys = [];

    /**
     * 构造函数.
     *
     * @throws \UnexpectedValueException
     */
    public function __construct(array $data, bool $ignoreMissingValues = true)
    {
        $this->ignoreMissingValues = $ignoreMissingValues;
        static::propertysCache($className = static::class);
        $this->fillDefaultValueWhenConstruct();
        foreach ($data as $prop => $value) {
            $camelizeProp = static::camelizePropertyName($prop);
            if (isset(static::$propertysCached[$className][$camelizeProp])) {
                $this->transformValueWhenConstruct($camelizeProp, $value);
                unset($data[$prop]);
            }
        }

        if (!$this->ignoreMissingValues && $data) {
            $e = sprintf('Public properties `%s` of data transfer object `%s` was not defined.', implode(',', array_keys($data)), $className);
    
            throw new UnexpectedValueException($e);
        }

        // 遍历校验所有公共属性值是否初始化
        $this->all();
    }

    /**
     * 构造时填充默认值.
     */
    protected function fillDefaultValueWhenConstruct(): void
    {
        foreach (static::$propertysCached[static::class] as $camelizeProp => $v) {
            if (method_exists($this, $defaultValueMethod = $camelizeProp.'DefaultValue')) {
                $this->{$camelizeProp} = $this->{$defaultValueMethod}();
            }
        }
    }

    /**
     * 构造时转换值.
     */
    protected function transformValueWhenConstruct(string $camelizeProp, mixed $value): void
    {
        if (true === $this->ignoreNullValue && null === $value) {
            return;
        }

        if (method_exists($this, $transformValueMethod = $camelizeProp.'TransformValue')) {
            $this->{$camelizeProp} = $this->{$transformValueMethod}($value);
        } else {
            $this->{$camelizeProp} = $value;
        }
    }

    /**
     * 从数组创建数据传输对象.
     */
    public static function fromArray(array $data, bool $ignoreMissingValues = true): static
    {
        return new static($data, $ignoreMissingValues);
    }

    /**
     * 从数组创建严格的数据传输对象.
     * 
     * - 不能忽略传入数据的丢失的值
     */
    public static function strict(array $data): static
    {
        return new static($data, false);
    }

    /**
     * 从数组或者数据传输对象创建不可变数据传输对象.
     */
    public static function immutable(array|self $data, bool $ignoreMissingValues = true): object 
    {
        if (!is_object($data)) {
            $data = new static($data, $ignoreMissingValues);
        }

        return new class ($data) implements IArray, ArrayAccess
        {
            /**
             * 构造函数.
             */
            public function __construct(private Dto $dto)
            {
            }
        
            /**
             * 实现魔术方法 __set.
             * 
             * @throws \UnexpectedValueException
             */
            public function __set(string $name, mixed $value): void
            {
                $e = sprintf('You cannot modify value of the public property `%s` of an immutable data transfer object.', $name);
                throw new UnexpectedValueException($e);
            }
        
            /**
             * 实现魔术方法 __get.
             */
            public function __get(string $name): mixed
            {
                return $this->dto->{$name};
            }

            /**
             * 实现魔术方法 __isset.
             */
            public function __isset(string $prop): bool
            {
                return $this->offsetExists($prop);
            }

            /**
             * 实现魔术方法 __unset.
             */
            public function __unset(string $prop): void
            {
                $this->__set($prop, null);
            }
        
            /**
             * 实现魔术方法 __call.
             */
            public function __call(string $method, array $args): mixed
            {
                return $this->dto->{$method}(...$args);
            }

            /**
             * {@inheritDoc}
             */
            public function toArray(): array
            {
                return $this->dto->toArray();
            }

            /**
             * {@inheritDoc}
             */
            public function offsetExists(mixed $index): bool
            {
                return $this->dto->offsetExists($index);
            }

            /**
             * {@inheritDoc}
             */
            public function offsetGet(mixed $index): mixed
            {
                return $this->dto->offsetGet($index);
            }

            /**
             * {@inheritDoc}
             */
            public function offsetSet(mixed $index, mixed $newval): void
            {
                $this->__set($index, $newval);
            }

            /**
             * {@inheritDoc}
             */
            public function offsetUnset(mixed $index): void
            {
                $this->__set($index, null);
            }
        };
    }

    /**
     * 设置白名单属性.
     */
    public function only(array $onlyPropertys, bool $overrideProperty = false): static 
    {
        $dto = clone $this;
        $dto->onlyPropertys = $overrideProperty ? $onlyPropertys : [...$this->onlyPropertys, ...$onlyPropertys];

        return $dto;
    }

    /**
     * 设置黑名单属性.
     */
    public function except(array $excepPropertys, bool $overrideProperty = false): static
    {
        $dto = clone $this;
        $dto->excepPropertys = $overrideProperty ? $excepPropertys : [...$this->excepPropertys, ...$excepPropertys];

        return $dto;
    }

    /**
     * 获取全部属性数据.
     */
    public function all(bool $unCamelizeStyle = true): array
    {
        $data = [];
        foreach (static::$propertysCached[static::class] as $prop => $unCamelizeProp) {
            $data[$unCamelizeStyle ? $unCamelizeProp : $prop] = $this->{$prop};
        }

        return $data;
    }

    /**
     * 设置驼峰属性命名风格.
     */
    public function camelizeNamingStyle(): static
    {
        $dto = clone $this;
        $dto->unCamelizeNamingStyle = false;

        return $dto;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $all = $this->all($unCamelizeNamingStyle = $this->unCamelizeNamingStyle);
        if ($this->onlyPropertys) {
            $all = only($all, $this->convertPropertyNamingStyle($this->onlyPropertys, $unCamelizeNamingStyle));
        } else {
            $all = except($all, $this->convertPropertyNamingStyle($this->excepPropertys, $unCamelizeNamingStyle));
        }

        return array_map(function ($value) use ($unCamelizeNamingStyle) {
            return $value instanceof IArray ?
                    ($value instanceof self && !$unCamelizeNamingStyle ? 
                        $value->camelizeNamingStyle()->toArray() : 
                        $value->toArray()) : 
                    $value;
        }, $all);
    }

    /**
     * 转换属性命名风格.
     */
    protected function convertPropertyNamingStyle(array $propertys, bool $unCamelizeNamingStyle)
    {
        if (!$unCamelizeNamingStyle) {
            return array_map(
                fn(string $property) => static::camelizePropertyName($property),
                $propertys,
            );
        }

        return array_map(
            fn(string $property) => static::unCamelizePropertyName($property),
            $propertys,
        );
    }

    /**
     * 实现魔术方法 __get.
     */
    public function __get(string $prop): mixed
    {
        return $this->offsetGet($prop);
    }

    /**
     * 实现魔术方法 __set.
     */
    public function __set(string $prop, mixed $value): void
    {
        $this->offsetSet($prop, $value);
    }

    /**
     * 实现魔术方法 __isset.
     */
    public function __isset(string $prop): bool
    {
        return $this->offsetExists($prop);
    }

    /**
     * 实现魔术方法 __unset.
     */
    public function __unset(string $prop): void
    {
        $this->offsetUnset($prop);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists(mixed $index): bool
    {
        return isset($this->{$this->validateCamelizeProperty($index)});
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet(mixed $index): mixed
    {
        return $this->{$this->validateCamelizeProperty($index)};
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet(mixed $index, mixed $newval): void
    {
        $this->{$this->validateCamelizeProperty($index)} = $newval; 
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset(mixed $index): void
    {
        $this->{$this->validateCamelizeProperty($index)} = null;
    }

    /**
     * 验证驼峰风格属性.
     * 
     * @throws \UnexpectedValueException
     */
    protected function validateCamelizeProperty(string $prop)
    {
        $className = static::class;
        $camelizeProp = static::camelizePropertyName($prop);
        if(!isset(static::$propertysCached[$className][$camelizeProp])) {
            $e = sprintf('Public properties `%s` of data transfer object `%s` was not defined.', $camelizeProp, $className);
    
            throw new UnexpectedValueException($e);
        }

        return $camelizeProp;
    }

    /**
     * 类属性数据缓存.
     */
    protected static function propertysCache(string $className): void 
    {
        static::$propertysCached[$className] = [];
        $reflectionClass = new ReflectionClass($className);
        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            if ($reflectionProperty->isStatic()) {
                continue;
            }
            $name = $reflectionProperty->getName();
            static::$propertysCached[$className][$name] = static::unCamelizePropertyName($name);
        }
    }

    /**
     * 统一处理前转换下划线命名风格.
     */
    protected static function unCamelizePropertyName(string $property): string
    {
        if (isset(static::$unCamelizePropertyNameCached[$property])) {
            return static::$unCamelizePropertyNameCached[$property];
        }

        return static::$unCamelizePropertyNameCached[$property] = un_camelize($property);
    }

    /**
     * 返回转驼峰命名.
     */
    protected static function camelizePropertyName(string $property): string
    {
        if (isset(static::$camelizePropertyNameCached[$property])) {
            return static::$camelizePropertyNameCached[$property];
        }

        return static::$camelizePropertyNameCached[$property] = camelize($property);
    }
}

// import fn.
class_exists(un_camelize::class);
class_exists(camelize::class);
class_exists(only::class);
class_exists(except::class);
