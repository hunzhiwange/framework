<?php

declare(strict_types=1);

namespace Leevel\Support;

use UnexpectedValueException;

/**
 * 枚举.
 *
 * - msg 分组用于实例枚举，未设置注解将会被忽略
 * - 多分组可以用于将多个相关的值放置一起维护
 */
abstract class Enum
{
    use BaseEnum;

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
        $value = static::normalizeEnumValue($value, 'msg');
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
     * 获取当前枚举值的键.
     */
    public function getKey(): string
    {
        return static::searchKey($this->value);
    }

    /**
     * 比较两个枚举是否完全相同.
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
}
