<?php

declare(strict_types=1);

namespace Leevel\Support;

use Closure;
use OutOfBoundsException;
use ReflectionClass;
use ReflectionClassConstant;
use TypeError;
use ValueError;

/**
 * 基础枚举.
 *
 * - msg 分组用于实例枚举，未设置注解将会被忽略
 * - 多分组可以用于将多个相关的值放置一起维护
 */
trait Enum
{
    /**
     * 验证是否为有效的枚举值.
     */
    public static function isValid(null|bool|float|int|string|self $value, string $group = 'msg'): bool
    {
        static::convertEnumValue($value);

        return in_array(
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
            return defined(static::class.'::'.$key);
        }

        foreach (static::cases() as $v) {
            if ($v->name === $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取给定值的键.
     */
    public static function searchKey(null|bool|float|int|string|self $value, string $group = 'msg'): string|false
    {
        static::convertEnumValue($value);

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
    public static function description(null|bool|float|int|string|self $value, string $group = 'msg'): string
    {
        static::convertEnumValue($value);
        $value = static::normalizeEnumValue($value, $group);
        $data = static::descriptions($group);

        return false !== ($key = array_search($value, $data['value'], true)) ?
                $data['description'][$key] :
                throw new OutOfBoundsException(
                    sprintf('Value `%s` is not part of %s:%s', $value, static::class, $group)
                );
    }

    /**
     * 获取分组枚举描述.
     *
     * - 未指定分组则获取全部描述
     *
     * @throws \OutOfBoundsException
     */
    public static function descriptions(string $group = ''): array
    {
        $descriptionsCached = static::descriptionsCache($className = static::class);

        if ($group) {
            return $descriptionsCached[$group] ??
                    throw new OutOfBoundsException(
                        sprintf('Group `%s` is not part of %s', $group, $className)
                    );
        }

        return $descriptionsCached;
    }

    /**
     * 获取分组枚举值.
     */
    public static function values(string $group = 'msg'): array
    {
        return array_values(static::descriptions($group)['value']);
    }

    /**
     * 获取分组枚举值和描述映射.
     */
    public static function valueDescriptionMap(string $group = 'msg', Closure $format = null): array
    {
        $descriptions = static::descriptions($group);
        $isEnum = enum_exists(static::class);
        $map = [];
        foreach ($descriptions['value'] as $k => $v) {
            $map[$isEnum ? $v->value : $v] = $descriptions['description'][$k];
        }
        if (!$format) {
            return $map;
        }

        $newMap = [];
        array_walk($map, function (mixed $value, string|int $key) use (&$newMap, $format) {
            $format($newMap, $key, $value);
        });

        return $newMap;
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
                $value = static::from($value);
            } catch (TypeError $e) {
                // 枚举值只能是整型或者字符串，这里兼容一下
                $value = static::from(is_string($value) ? (int) $value: $value);
            }
        } catch (ValueError $e) {
            throw new OutOfBoundsException($e->getMessage(), $e->getCode());
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
        foreach ((new ReflectionClass($className))->getConstants(ReflectionClassConstant::IS_PUBLIC) as $key => $value) {
            foreach ((new ReflectionClassConstant($className, $key))->getAttributes() as $attribute) {
                $group = $attribute->getName();
                $group = false === str_contains($group, '\\') ? $group :
                    substr($group, strripos($group, '\\') + 1);
                $descriptionsCached[$className][$group]['value'][$key] = $value;
                $descriptionsCached[$className][$group]['description'][$key] = $attribute->getArguments()[0] ?? '';
            }
        }

        return $descriptionsCached[$className];
    }
}
