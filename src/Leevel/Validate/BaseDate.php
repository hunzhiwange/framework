<?php

declare(strict_types=1);

namespace Leevel\Validate;

/**
 * 日期验证.
 */
trait BaseDate
{
    /**
     * 数据验证器.
     */
    protected static ?IValidator $validator = null;

    /**
     * 校验日期.
     *
     * @throws \InvalidArgumentException
     */
    protected static function validateDate(mixed $value, array $param, IValidator $validator, string $field, bool $before = false): bool
    {
        if (!\is_string($value)) {
            return false;
        }

        if (!\array_key_exists(0, $param)) {
            throw new \InvalidArgumentException('Missing the first element of param.');
        }

        static::$validator = $validator;

        if ($format = static::getDateFormat($field)) {
            return static::doWithFormat($format, $value, $param, $before);
        }

        if (!($time = strtotime($param[0]))) {
            if (null === ($_ = $validator->getFieldValue($param[0]))) {
                return false;
            }

            // @phpstan-ignore-next-line
            return static::compareTime(strtotime($value), strtotime($_), $before);
        }

        // @phpstan-ignore-next-line
        return static::compareTime(strtotime($value), $time, $before);
    }

    /**
     * 比较时间.
     */
    protected static function compareTime(int $left, int $right, bool $before = false): bool
    {
        if ($before) {
            return $left < $right;
        }

        return $left > $right;
    }

    /**
     * 获取时间格式化.
     */
    protected static function getDateFormat(string $field): ?string
    {
        // @phpstan-ignore-next-line
        if ($result = static::$validator->getParseRule($field, 'date_format')) {
            return $result[1][0];
        }

        return null;
    }

    /**
     * 验证在给定日期之后.
     */
    protected static function doWithFormat(string $format, mixed $value, array $param, bool $before = false): bool
    {
        // @phpstan-ignore-next-line
        $param[0] = static::$validator->getFieldValue($param[0]) ?: $param[0];

        if ($before) {
            [$param[0], $value] = [$value, $param[0]];
        }

        return static::doCheckDate($format, (string) $param[0], (string) $value);
    }

    /**
     * 验证日期顺序.
     */
    protected static function doCheckDate(string $format, string $first, string $second): bool
    {
        $before = static::makeDateTimeFormat($format, $first);
        $after = static::makeDateTimeFormat($format, $second);

        return $before && $after && $before < $after;
    }

    /**
     * 创建 DateTime 实例.
     */
    protected static function makeDateTimeFormat(string $format, string $value): ?\DateTime
    {
        $date = \DateTime::createFromFormat($format, $value);
        if ($date) {
            return $date;
        }

        try {
            return new \DateTime($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
