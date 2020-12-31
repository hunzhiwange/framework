<?php

declare(strict_types=1);

namespace Leevel\Support;

use OutOfBoundsException;
use ReflectionClass;
use ReflectionClassConstant;
use UnexpectedValueException;

/**
 * 枚举.
 * 
 * - `msg` 注解和没有注解表示表示枚举值，其它分组不属于枚举
 * - 多分组可以用于将多个相关的值放置一起维护，比如实体的多个字段的不同枚举
 */
abstract class Enum
{
    /**
     * 类描述数据缓存.
     */
    public static array $descriptionsCached = [];

    /**
     * 当前枚举值.
     */
    protected null|bool|float|int|string $value;

    /**
     * 构造函数.
     * 
     * @throws \UnexpectedValueException
     */
    public function __construct(null|bool|float|int|string $value) 
    {
        if (!static::isValid($value)) {
            $e = sprintf('Value `%s` is not part of %s', $value, static::class);
            throw new UnexpectedValueException($e);
        }

        $this->value = $value;
    }

    /**
     * 获取当前枚举值.
     */
    public function getValue(): null|bool|float|int|string
    {
        return $this->value;
    }

    /**
     * 两个枚举是否完全相同.
     */
    public function equals(self $enum): bool
    {
        return static::class === get_class($enum) &&
                $this->getValue() === $enum->getValue();
    }

    /**
     * 实现魔术方法 __toString.
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * 验证是否为有效的枚举值. 
     */
    public static function isValid(null|bool|float|int|string $value, string $group = 'msg'): bool
    {
        return isset(static::getDescriptions($group)[$value]);
    }

    /**
     * 验证是否为有效的键.
     */
    public static function isValidKey(string $key): bool
    {
        return defined(static::class.'::'.$key);
    }

    /**
     * 获取枚举值对应的描述.
     * 
     * @throws \OutOfBoundsException
     */
    public static function getDescription(null|bool|float|int|string $value, string $group = 'msg'): string
    {
        return static::getDescriptions($group)[$value] ?? 
            throw new OutOfBoundsException(
                sprintf('Value `%s` is not part of %s group %s', $value, static::class, $group)
            );
    }

    /**
     * 获取分组枚举描述.
     * 
     * - 未指定分组则获取全部描述
     * 
     * @throws \OutOfBoundsException
     */
    public static function getDescriptions(?string $group = null): array
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
     * 类描述数据缓存.
     */
    protected static function descriptionsCache(string $className): void 
    {
        static::$descriptionsCached[$className] = [];
        $refClass = new ReflectionClass($className);
        foreach ($refClass->getConstants() as $key => $value) {
            $refConstant = new ReflectionClassConstant($className, $key);
            if (!$attributes = $refConstant->getAttributes()) {
                static::$descriptionsCached[$className]['msg'][$value] = '';
                continue;
            }
            
            foreach ($attributes as $attribute) {
                $group = $attribute->getName();
                $group = false === str_contains($group, '\\') ? $group :
                            substr($group, strripos($group, '\\')+1);
                static::$descriptionsCached[$className][$group][$value] = $attribute->getArguments()[0] ?? '';
            }
        }
    }
}
