<?php

declare(strict_types=1);

namespace Leevel\Support;

use Leevel\Support\Arr\Except;
use Leevel\Support\Arr\Only;
use Leevel\Support\Str\Camelize;
use Leevel\Support\Str\UnCamelize;

/**
 * 数据传输对象.
 */
abstract class Dto implements IArray, \ArrayAccess
{
    /**
     * 驼峰法命名属性缓存.
     */
    protected static array $camelizePropertiesNameCachedInternal = [];

    /**
     * 下划线命名属性缓存.
     */
    protected static array $unCamelizePropertiesNameCachedInternal = [];

    /**
     * 类属性数据缓存.
     */
    protected static array $propertiesCachedInternal = [];

    /**
     * 初始化忽略丢失的值.
     */
    protected bool $ignoreMissingValuesInternal = true;

    /**
     * 初始化忽略 NULL 值.
     */
    protected bool $ignoreNullValueInternal = true;

    /**
     * 忽略内置类型值转换.
     */
    protected bool $ignoreBuiltinTransformValueInternal = false;

    /**
     * 下划线命名风格.
     */
    protected bool $unCamelizeNamingStyleInternal = true;

    /**
     * 黑名单属性.
     */
    protected array $exceptPropertiesInternal = [];

    /**
     * 白名单属性.
     */
    protected array $onlyPropertiesInternal = [];

    /**
     * 转换数组时忽略 NULL 值.
     */
    protected bool $ignoreNullValueWhenToArrayInternal = false;

    /**
     * 构造函数.
     *
     * @throws \UnexpectedValueException
     */
    public function __construct(array $data = [], bool $ignoreMissingValues = true)
    {
        $this->ignoreMissingValuesInternal = $ignoreMissingValues;
        static::propertiesCache($className = static::class);
        $this->fillDefaultValueWhenConstruct();
        foreach ($data as $prop => $value) {
            $camelizeProp = static::camelizePropertiesName($prop);
            if (isset(static::$propertiesCachedInternal[$className]['name'][$camelizeProp])) {
                $this->transformValueWhenConstruct($camelizeProp, $value, static::$propertiesCachedInternal[$className]['type'][$camelizeProp]);
                unset($data[$prop]);
            }
        }

        if (!$this->ignoreMissingValuesInternal && $data) {
            $e = sprintf('Public properties `%s` of data transfer object `%s` was not defined.', implode(',', array_keys($data)), $className);

            throw new \UnexpectedValueException($e);
        }

        // 遍历校验所有公共属性值是否初始化
        $this->all();
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
    public function only(array $onlyProperties, bool $overrideProperties = false): static
    {
        $dto = clone $this;
        $dto->onlyPropertiesInternal = $overrideProperties ? $onlyProperties : [...$this->onlyPropertiesInternal, ...$onlyProperties];

        return $dto;
    }

    /**
     * 设置黑名单属性.
     */
    public function except(array $exceptProperties, bool $overrideProperties = false): static
    {
        $dto = clone $this;
        $dto->exceptPropertiesInternal = $overrideProperties ? $exceptProperties : [...$this->exceptPropertiesInternal, ...$exceptProperties];

        return $dto;
    }

    /**
     * 设置转换数组时忽略 NULL 值.
     */
    public function withoutNull(): static
    {
        $dto = clone $this;
        $dto->ignoreNullValueWhenToArrayInternal = true;

        return $dto;
    }

    /**
     * 获取全部属性数据.
     */
    public function all(bool $unCamelizeStyle = true): array
    {
        $data = [];
        foreach (static::$propertiesCachedInternal[static::class]['name'] as $prop => $unCamelizeProp) {
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
        $dto->unCamelizeNamingStyleInternal = false;

        return $dto;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $all = $this->all($unCamelizeNamingStyle = $this->unCamelizeNamingStyleInternal);
        if ($this->onlyPropertiesInternal) {
            $all = Only::handle($all, $this->convertPropertyNamingStyle($this->onlyPropertiesInternal, $unCamelizeNamingStyle));
        } else {
            $all = Except::handle($all, $this->convertPropertyNamingStyle($this->exceptPropertiesInternal, $unCamelizeNamingStyle));
        }

        $all = array_map(function ($value) use ($unCamelizeNamingStyle) {
            return $value instanceof IArray ?
                    ($value instanceof self && !$unCamelizeNamingStyle ?
                        $value->camelizeNamingStyle()->toArray() :
                        $value->toArray()) :
                    $value;
        }, $all);

        if (!$this->ignoreNullValueWhenToArrayInternal) {
            return $all;
        }

        return array_filter($all, function ($v) {
            return null !== $v;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->{$this->validateCamelizeProperties($offset)});
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$this->validateCamelizeProperties($offset)};
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$this->validateCamelizeProperties($offset)} = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->{$this->validateCamelizeProperties($offset)} = null;
    }

    /**
     * 构造时填充默认值.
     */
    protected function fillDefaultValueWhenConstruct(): void
    {
        foreach (static::$propertiesCachedInternal[static::class]['name'] as $camelizeProp => $v) {
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
        if ($this->ignoreNullValueInternal && null === $value) {
            return;
        }

        if (method_exists($this, $transformValueMethod = $camelizeProp.'TransformValue')) {
            $this->{$camelizeProp} = $this->{$transformValueMethod}($value);
        } elseif (!$this->ignoreBuiltinTransformValueInternal && $defaultType && method_exists($this, $builtinTransformValueMethod = $defaultType.'BuiltinTransformValue')) {
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
                fn (string $property) => static::camelizePropertiesName($property),
                $propertys,
            );
        }

        return array_map(
            fn (string $property) => static::unCamelizePropertiesName($property),
            $propertys,
        );
    }

    /**
     * 验证驼峰风格属性.
     *
     * @throws \UnexpectedValueException
     */
    protected function validateCamelizeProperties(string $prop): string
    {
        $className = static::class;
        $camelizeProp = static::camelizePropertiesName($prop);
        if (!isset(static::$propertiesCachedInternal[$className]['name'][$camelizeProp])) {
            $e = sprintf('Public properties `%s` of data transfer object `%s` was not defined.', $camelizeProp, $className);

            throw new \UnexpectedValueException($e);
        }

        return $camelizeProp;
    }

    /**
     * 类属性数据缓存.
     */
    protected static function propertiesCache(string $className): void
    {
        static::$propertiesCachedInternal[$className] = [];

        /** @phpstan-ignore-next-line */
        $reflectionClass = new \ReflectionClass($className);
        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            if ($reflectionProperty->isStatic()) {
                continue;
            }

            $name = $reflectionProperty->getName();
            $propertyType = null;
            if (($reflectionType = $reflectionProperty->getType())
                && !$reflectionType instanceof \ReflectionUnionType
                // @phpstan-ignore-next-line
                && $reflectionType->isBuiltin()) {
                /** @phpstan-ignore-next-line */
                $propertyType = $reflectionType->getName();
            }
            static::$propertiesCachedInternal[$className]['name'][$name] = static::unCamelizePropertiesName($name);
            static::$propertiesCachedInternal[$className]['type'][$name] = $propertyType;
        }
    }

    /**
     * 统一处理前转换下划线命名风格.
     */
    protected static function unCamelizePropertiesName(string $property): string
    {
        if (isset(static::$unCamelizePropertiesNameCachedInternal[$property])) {
            return static::$unCamelizePropertiesNameCachedInternal[$property];
        }

        return static::$unCamelizePropertiesNameCachedInternal[$property] = UnCamelize::handle($property);
    }

    /**
     * 返回转驼峰命名.
     */
    protected static function camelizePropertiesName(string $property): string
    {
        if (isset(static::$camelizePropertiesNameCachedInternal[$property])) {
            return static::$camelizePropertiesNameCachedInternal[$property];
        }

        return static::$camelizePropertiesNameCachedInternal[$property] = Camelize::handle($property);
    }
}
