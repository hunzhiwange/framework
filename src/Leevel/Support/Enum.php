<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * 基础枚举.
 *
 * - \Leevel\Support\Msg 分组用于实例枚举，未设置注解将会被忽略
 * - 多分组可以用于将多个相关的值放置一起维护
 */
trait Enum
{
    /**
     * 验证是否为有效的枚举值.
     */
    public static function isValid(null|bool|float|int|string|self $value, string $group = Msg::class): bool
    {
        try {
            static::convertEnumValue($value);
        } catch (\OutOfBoundsException) {
            return false;
        }

        return \in_array(
            static::normalizeEnumValue($value, $group),
            static::descriptions($group)['value'],
            true
        );
    }

    /**
     * 验证是否为有效的键.
     */
    public static function isValidKey(string $key): bool
    {
        if (!enum_exists(static::class)) {
            return \defined(static::class.'::'.$key);
        }

        foreach (static::cases() as $v) {
            if ($v->name === $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * 根据键值获取值.
     */
    public static function valueByKey(string $key): mixed
    {
        if (!enum_exists(static::class)) {
            return \defined(static::class.'::'.$key) ?
                \constant(static::class.'::'.$key) :
                throw new \OutOfBoundsException(
                    sprintf('Key `%s` is not part of %s', $key, static::class)
                );
        }

        foreach (static::cases() as $v) {
            if ($v->name === $key) {
                return $v->value ??
                    throw new \OutOfBoundsException(
                        sprintf('Key `%s` of %s does not have a value.', $key, static::class)
                    );
            }
        }

        throw new \OutOfBoundsException(
            sprintf('Key `%s` is not part of %s', $key, static::class)
        );
    }

    /**
     * 获取给定值的键.
     */
    public static function searchKey(null|bool|float|int|string|self $value, string $group = Msg::class): false|int|string
    {
        try {
            static::convertEnumValue($value);
        } catch (\OutOfBoundsException) {
            return false;
        }

        return array_search(
            static::normalizeEnumValue($value, $group),
            static::descriptions($group)['value'],
            true
        );
    }

    /**
     * 获取枚举值对应的描述.
     *
     * @throws \OutOfBoundsException
     */
    public static function description(null|bool|float|int|string|self $value, string $group = Msg::class): string
    {
        static::convertEnumValue($value);
        $normalizeValue = static::normalizeEnumValue($value, $group);
        $data = static::descriptions($group);

        return false !== ($key = array_search($normalizeValue, $data['value'], true)) ?
                $data['description'][$key] :
                throw new \OutOfBoundsException(
                    sprintf('Value `%s` is not part of %s:%s', $value, static::class, $group)
                );
    }

    /**
     * 获取描述对应的枚举值.
     *
     * @throws \OutOfBoundsException
     */
    public static function value(string $description, string $group = Msg::class): null|bool|float|int|string|self
    {
        $data = static::descriptions($group);

        return false !== ($key = array_search($description, $data['description'], true)) ?
            $data['value'][$key] :
            throw new \OutOfBoundsException(
                sprintf('Description `%s` is not part of %s:%s', $description, static::class, $group)
            );
    }

    /**
     * 获取分组枚举描述.
     *
     * - 未指定分组则获取全部描述
     *
     * @throws \OutOfBoundsException
     */
    public static function descriptions(string $group = Msg::class): array
    {
        $descriptionsCached = static::descriptionsCache($className = static::class);

        if ($group) {
            return $descriptionsCached[$group] ??
                    throw new \OutOfBoundsException(
                        sprintf('Group `%s` is not part of %s', $group, $className)
                    );
        }

        return $descriptionsCached;
    }

    /**
     * 获取分组枚举名字.
     */
    public static function names(string $group = Msg::class): array
    {
        return array_keys(static::descriptions($group)['value'] ?? []);
    }

    /**
     * 获取分组枚举值.
     */
    public static function values(string $group = Msg::class): array
    {
        $value = array_values(static::descriptions($group)['value'] ?? []);
        if (!enum_exists(static::class)) {
            return $value;
        }

        foreach ($value as &$v) {
            $v = $v->value;
        }

        return $value;
    }

    /**
     * 获取分组枚举值和描述映射.
     */
    public static function valueDescription(string $group = Msg::class): array
    {
        $descriptions = static::descriptions($group);
        $isEnum = enum_exists(static::class);
        $map = [];
        $index = 0;
        foreach ($descriptions['value'] as $k => $v) {
            // 没有值的枚举直接采用索引输出
            $map[$isEnum ? ($v->value ?? $index) : $v] = $descriptions['description'][$k];
            ++$index;
        }

        return $map;
    }

    /**
     * 整理枚举值.
     *
     * - 可用于将数据库中的值转换成标准类型
     */
    protected static function normalizeEnumValue(null|bool|float|int|string|self $value, string $group): null|bool|float|int|string|self
    {
        return $value;
    }

    /**
     * 底层枚举值转换.
     *
     * - 可用于将数字或者字符串转换为对应的枚举值
     */
    protected static function convertEnumValue(null|bool|float|int|string|self &$value): void
    {
        // 枚举特殊处理一下值
        if (!enum_exists(static::class) || $value instanceof self) {
            return;
        }

        try {
            try {
                /** @phpstan-ignore-next-line */
                $value = static::from($value);
            } catch (\TypeError $e) {
                // 枚举值只能是整型或者字符串，这里兼容一下
                if (\is_string($value) && !ctype_digit($value)) {
                    throw new \OutOfBoundsException('Invalid enum value.');
                }

                /** @phpstan-ignore-next-line */
                $value = static::from(\is_string($value) ? (int) $value : $value);
            }
        } catch (\ValueError $e) {
            throw new \OutOfBoundsException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 类描述数据缓存.
     */
    protected static function descriptionsCache(string $className): array
    {
        static $descriptionsCached = [];

        if (isset($descriptionsCached[$className])) {
            return $descriptionsCached[$className];
        }

        $descriptionsCached[$className] = [];
        // @phpstan-ignore-next-line
        foreach ((new \ReflectionClass($className))->getConstants(\ReflectionClassConstant::IS_PUBLIC) as $key => $value) {
            foreach ((new \ReflectionClassConstant($className, $key))->getAttributes() as $attribute) {
                $group = $attribute->getName();
                if (class_exists($group)) {
                    /** @phpstan-ignore-next-line */
                    $description = $attribute->newInstance()() ?? '';
                } else {
                    $group = false === str_contains($group, '\\') ? $group :
                        substr($group, strripos($group, '\\') + 1);
                    $description = $attribute->getArguments()[0] ?? '';
                }
                $descriptionsCached[$className][$group]['value'][$key] = $value;
                $descriptionsCached[$className][$group]['description'][$key] = $description;
            }
        }

        return $descriptionsCached[$className];
    }
}
