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
    protected static array $camelizePropertiesNameCachedFramework = [];

    /**
     * 下划线命名属性缓存.
     */
    protected static array $unCamelizePropertiesNameCachedFramework = [];

    /**
     * 类属性数据缓存.
     */
    protected static array $propertiesCachedFramework = [];

    /**
     * 初始化忽略丢失的值.
     */
    protected bool $ignoreMissingValuesFramework = true;

    /**
     * 初始化忽略 NULL 值.
     */
    protected bool $ignoreNullValueFramework = true;

    /**
     * 忽略内置类型值转换.
     */
    protected bool $ignoreBuiltinTransformValueFramework = false;

    /**
     * 下划线命名风格.
     */
    protected bool $unCamelizeNamingStyleFramework = true;

    /**
     * 黑名单属性.
     */
    protected array $exceptPropertiesFramework = [];

    /**
     * 白名单属性.
     */
    protected array $onlyPropertiesFramework = [];

    /**
     * 转换数组时忽略 NULL 值.
     */
    protected bool $ignoreNullValueWhenToArrayFramework = false;

    /**
     * 构造函数.
     *
     * @throws \UnexpectedValueException
     */
    public function __construct(array $data = [], bool $ignoreMissingValues = true)
    {
        $this->ignoreMissingValuesFramework = $ignoreMissingValues;
        static::propertiesCache($className = static::class);
        $this->fillDefaultValueWhenConstruct();
        foreach ($data as $prop => $value) {
            $camelizeProp = static::camelizePropertiesName($prop);
            if (isset(static::$propertiesCachedFramework[$className]['name'][$camelizeProp])) {
                $this->transformValueWhenConstruct($camelizeProp, $value, static::$propertiesCachedFramework[$className]['type'][$camelizeProp]);
                unset($data[$prop]);
            }
        }

        if (!$this->ignoreMissingValuesFramework && $data) {
            throw new \UnexpectedValueException(sprintf('Public properties `%s` of data transfer object `%s` was not defined.', implode(',', array_keys($data)), $className));
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
     *
     * - 代码格式化工具自动将 unset($obj->foo) 修改为 $obj->foo = null
     *
     * @codeCoverageIgnore
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
        $dto->onlyPropertiesFramework = $overrideProperties ? $onlyProperties : [...$this->onlyPropertiesFramework, ...$onlyProperties];

        return $dto;
    }

    /**
     * 设置黑名单属性.
     */
    public function except(array $exceptProperties, bool $overrideProperties = false): static
    {
        $dto = clone $this;
        $dto->exceptPropertiesFramework = $overrideProperties ? $exceptProperties : [...$this->exceptPropertiesFramework, ...$exceptProperties];

        return $dto;
    }

    /**
     * 设置转换数组时忽略 NULL 值.
     */
    public function withoutNull(): static
    {
        $dto = clone $this;
        $dto->ignoreNullValueWhenToArrayFramework = true;

        return $dto;
    }

    /**
     * 获取全部属性数据.
     */
    public function all(bool $unCamelizeStyle = true): array
    {
        $data = [];
        foreach (static::$propertiesCachedFramework[static::class]['name'] as $prop => $unCamelizeProp) {
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
        $dto->unCamelizeNamingStyleFramework = false;

        return $dto;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $all = $this->all($unCamelizeNamingStyle = $this->unCamelizeNamingStyleFramework);
        if ($this->onlyPropertiesFramework) {
            $all = Only::handle($all, $this->convertPropertyNamingStyle($this->onlyPropertiesFramework, $unCamelizeNamingStyle));
        } else {
            $all = Except::handle($all, $this->convertPropertyNamingStyle($this->exceptPropertiesFramework, $unCamelizeNamingStyle));
        }

        $all = array_map(function ($value) use ($unCamelizeNamingStyle) {
            return $value instanceof IArray ?
                    ($value instanceof self && !$unCamelizeNamingStyle ?
                        $value->camelizeNamingStyle()->toArray() :
                        $value->toArray()) :
                    $value;
        }, $all);

        if (!$this->ignoreNullValueWhenToArrayFramework) {
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
        foreach (static::$propertiesCachedFramework[static::class]['name'] as $camelizeProp => $v) {
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
        if ($this->ignoreNullValueFramework && null === $value) {
            return;
        }

        if (method_exists($this, $transformValueMethod = $camelizeProp.'TransformValue')) {
            $this->{$camelizeProp} = $this->{$transformValueMethod}($value);
        } elseif (!$this->ignoreBuiltinTransformValueFramework && $defaultType && method_exists($this, $builtinTransformValueMethod = $defaultType.'BuiltinTransformValue')) {
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

    protected function floatBuiltinTransformValue(mixed $value): float
    {
        return (float) $value;
    }

    protected function boolBuiltinTransformValue(mixed $value): bool
    {
        return (bool) $value;
    }

    protected function arrayBuiltinTransformValue(mixed $value): array
    {
        return (array) $value;
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
        if (!isset(static::$propertiesCachedFramework[$className]['name'][$camelizeProp])) {
            throw new \UnexpectedValueException(sprintf('Public properties `%s` of data transfer object `%s` was not defined.', $camelizeProp, $className));
        }

        return $camelizeProp;
    }

    /**
     * 类属性数据缓存.
     */
    protected static function propertiesCache(string $className): void
    {
        if (isset(static::$propertiesCachedFramework[$className])) {
            return;
        }

        static::$propertiesCachedFramework[$className] = [
            'type' => [],
            'name' => [],
        ];

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
            static::$propertiesCachedFramework[$className]['name'][$name] = static::unCamelizePropertiesName($name);
            static::$propertiesCachedFramework[$className]['type'][$name] = $propertyType;
        }
    }

    /**
     * 统一处理前转换下划线命名风格.
     */
    protected static function unCamelizePropertiesName(string $property): string
    {
        if (isset(static::$unCamelizePropertiesNameCachedFramework[$property])) {
            return static::$unCamelizePropertiesNameCachedFramework[$property];
        }

        return static::$unCamelizePropertiesNameCachedFramework[$property] = UnCamelize::handle($property);
    }

    /**
     * 返回转驼峰命名.
     */
    protected static function camelizePropertiesName(string $property): string
    {
        if (isset(static::$camelizePropertiesNameCachedFramework[$property])) {
            return static::$camelizePropertiesNameCachedFramework[$property];
        }

        return static::$camelizePropertiesNameCachedFramework[$property] = Camelize::handle($property);
    }
}
