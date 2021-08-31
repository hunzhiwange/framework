<?php

declare(strict_types=1);

namespace Leevel\Support;

use Closure;
use ReflectionClass;
use ReflectionClassConstant;
use OutOfBoundsException;

/**
 * 基础枚举.
 * 
 * - msg 分组用于实例枚举，未设置注解将会被忽略
 * - 多分组可以用于将多个相关的值放置一起维护
 */
trait BaseEnum
{
    /**
     * 类描述数据缓存.
     */
    protected static array $descriptionsCached = [];

    /**
     * 验证是否为有效的枚举值. 
     */
    public static function isValid(null|bool|float|int|string $value, string $group = 'msg'): bool
    {
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
        return defined(static::class.'::'.$key);
    }

    /**
     * 获取给定值的键.
     */
    public static function searchKey(null|bool|float|int|string $value, string $group = 'msg'): string|false
    {
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
    public static function description(null|bool|float|int|string $value, string $group = 'msg'): string
    {
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
        $className = static::class;
        if (!isset(static::$descriptionsCached[$className])) {
            static::descriptionsCache($className); 
        }

        if ($group) {
            return static::$descriptionsCached[$className][$group] ?? 
                    throw new OutOfBoundsException(
                        sprintf('Group `%s` is not part of %s', $group, $className)
                    );
        }
        
        return static::$descriptionsCached[$className];
    }

    /**
     * 获取分组枚举值.
     */
    public static function values(string $group): array
    {
        return array_values(static::descriptions($group)['value']);
    }

    /**
     * 获取分组枚举值和描述映射.
     */
    public static function valueDescriptionMap(string $group, Closure $format = null): array
    {
        $descriptions = static::descriptions($group);
        $map = [];
        foreach ($descriptions['value'] as $k => $v) {
            $map[$v] = $descriptions['description'][$k];
        }
        if (!$format) {
            return $map;
        }

        $newMap = [];
        array_walk($map, function(mixed $value, string|int $key) use(&$newMap, $format) {
            $format($newMap, $key, $value);
        });

        return $newMap;
    }

    /**
     * 整理枚举值.
     * 
     * - 可用于将数据库中的值转换成标准类型
     */
    protected static function normalizeEnumValue(null|bool|float|int|string &$value, string $group): null|bool|float|int|string 
    {
        return $value;
    }

    /**
     * 类描述数据缓存.
     */
    protected static function descriptionsCache(string $className): void 
    {
        static::$descriptionsCached[$className] = [];
        foreach ((new ReflectionClass($className))->getConstants(ReflectionClassConstant::IS_PUBLIC) as $key => $value) {
            foreach ((new ReflectionClassConstant($className, $key))->getAttributes() as $attribute) {
                $group = $attribute->getName();
                $group = false === str_contains($group, '\\') ? $group :
                            substr($group, strripos($group, '\\')+1);
                static::$descriptionsCached[$className][$group]['value'][$key] = $value;
                static::$descriptionsCached[$className][$group]['description'][$key] = $attribute->getArguments()[0] ?? '';
            }
        }
    }
}
