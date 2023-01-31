<?php

declare(strict_types=1);

namespace Leevel\Support;

use ArrayAccess;
use Leevel\Support\Arr\Except;
use Leevel\Support\Arr\Only;
use Leevel\Support\Str\Camelize;
use Leevel\Support\Str\UnCamelize;
use ReflectionClass;
use ReflectionProperty;
use ReflectionUnionType;
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
     * 初始化忽略丢失的值.
     */
    protected bool $ignoreMissingValues = true;

    /**
     * 初始化忽略 NULL 值.
     */
    protected bool $ignoreNullValue = true;

    /**
     * 忽略内置类型值转换.
     */
    protected bool $ignoreBuiltinTransformValue = false;

    /**
     * 下划线命名风格.
     */
    protected bool $unCamelizeNamingStyle = true;

    /**
     * 黑名单属性.
     */
    protected array $exceptPropertys = [];

    /**
     * 白名单属性.
     */
    protected array $onlyPropertys = [];

    /**
     * 转换数组时忽略 NULL 值.
     */
    protected bool $ignoreNullValueWhenToArray = false;

    /**
     * 构造函数.
     *
     * @throws \UnexpectedValueException
     */
    public function __construct(array $data = [], bool $ignoreMissingValues = true)
    {
        $this->ignoreMissingValues = $ignoreMissingValues;
        static::propertysCache($className = static::class);
        $this->fillDefaultValueWhenConstruct();
        foreach ($data as $prop => $value) {
            $camelizeProp = static::camelizePropertyName($prop);
            if (isset(static::$propertysCached[$className]['name'][$camelizeProp])) {
                $this->transformValueWhenConstruct($camelizeProp, $value, static::$propertysCached[$className]['type'][$camelizeProp]);
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
    public function except(array $exceptPropertys, bool $overrideProperty = false): static
    {
        $dto = clone $this;
        $dto->exceptPropertys = $overrideProperty ? $exceptPropertys : [...$this->exceptPropertys, ...$exceptPropertys];

        return $dto;
    }

    /**
     * 设置转换数组时忽略 NULL 值.
     */
    public function withoutNull(): static
    {
        $dto = clone $this;
        $dto->ignoreNullValueWhenToArray = true;

        return $dto;
    }

    /**
     * 获取全部属性数据.
     */
    public function all(bool $unCamelizeStyle = true): array
    {
        $data = [];
        foreach (static::$propertysCached[static::class]['name'] as $prop => $unCamelizeProp) {
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
            $all = Only::handle($all, $this->convertPropertyNamingStyle($this->onlyPropertys, $unCamelizeNamingStyle));
        } else {
            $all = Except::handle($all, $this->convertPropertyNamingStyle($this->exceptPropertys, $unCamelizeNamingStyle));
        }

        $all = array_map(function ($value) use ($unCamelizeNamingStyle) {
            return $value instanceof IArray ?
                    ($value instanceof self && !$unCamelizeNamingStyle ?
                        $value->camelizeNamingStyle()->toArray() :
                        $value->toArray()) :
                    $value;
        }, $all);

        if (!$this->ignoreNullValueWhenToArray) {
            return $all;
        }

        return array_filter($all, function ($v) {
            return null !== $v;
        });
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
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->{$this->validateCamelizeProperty($offset)});
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$this->validateCamelizeProperty($offset)};
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$this->validateCamelizeProperty($offset)} = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->{$this->validateCamelizeProperty($offset)} = null;
    }

    /**
     * 构造时填充默认值.
     */
    protected function fillDefaultValueWhenConstruct(): void
    {
        foreach (static::$propertysCached[static::class]['name'] as $camelizeProp => $v) {
            if (method_exists($this, $defaultValueMethod = $camelizeProp.'DefaultValue')) {
                $this->{$camelizeProp} = $this->{$defaultValueMethod}();
            }
        }
    }

    /**
     * 构造时转换值.
     */
    protected function transformValueWhenConstruct(string $camelizeProp, mixed $value, ?string $defaultType): void
    {
        if ($this->ignoreNullValue && null === $value) {
            return;
        }

        if (method_exists($this, $transformValueMethod = $camelizeProp.'TransformValue')) {
            $this->{$camelizeProp} = $this->{$transformValueMethod}($value);
        } elseif (!$this->ignoreBuiltinTransformValue && $defaultType && method_exists($this, $builtinTransformValueMethod = $defaultType.'BuiltinTransformValue')) {
            $this->{$camelizeProp} = $this->{$builtinTransformValueMethod}($value);
        } else {
            $this->{$camelizeProp} = $value;
        }
    }

    protected function intBuiltinTransformValue(mixed $value): int
    {
        return (int) $value;
    }

    protected function stringBuiltinTransformValue(mixed $value): string
    {
        return (string) $value;
    }

    /**
     * 转换属性命名风格.
     */
    protected function convertPropertyNamingStyle(array $propertys, bool $unCamelizeNamingStyle): array
    {
        if (!$unCamelizeNamingStyle) {
            return array_map(
                fn (string $property) => static::camelizePropertyName($property),
                $propertys,
            );
        }

        return array_map(
            fn (string $property) => static::unCamelizePropertyName($property),
            $propertys,
        );
    }

    /**
     * 验证驼峰风格属性.
     *
     * @throws \UnexpectedValueException
     */
    protected function validateCamelizeProperty(string $prop): string
    {
        $className = static::class;
        $camelizeProp = static::camelizePropertyName($prop);
        if (!isset(static::$propertysCached[$className]['name'][$camelizeProp])) {
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
            $propertyType = null;
            if (($reflectionType = $reflectionProperty->getType()) &&
                !$reflectionType instanceof ReflectionUnionType &&
                $reflectionType->isBuiltin()) {
                $propertyType = $reflectionType->getName();
            }
            static::$propertysCached[$className]['name'][$name] = static::unCamelizePropertyName($name);
            static::$propertysCached[$className]['type'][$name] = $propertyType;
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

        return static::$unCamelizePropertyNameCached[$property] = UnCamelize::handle($property);
    }

    /**
     * 返回转驼峰命名.
     */
    protected static function camelizePropertyName(string $property): string
    {
        if (isset(static::$camelizePropertyNameCached[$property])) {
            return static::$camelizePropertyNameCached[$property];
        }

        return static::$camelizePropertyNameCached[$property] = Camelize::handle($property);
    }
}
