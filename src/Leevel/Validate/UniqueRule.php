<?php

declare(strict_types=1);

namespace Leevel\Validate;

use InvalidArgumentException;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Select;
use function Leevel\Support\Type\arr;
use Leevel\Support\Type\arr;

/**
 * 不能重复值验证规则.
 */
class UniqueRule
{
    /**
     * 占位符.
    */
    public const PLACEHOLDER = '_';

    /**
     * 隔离符.
    */
    public const SEPARATE = ':';

    /**
     * 整型类型标识符.
    */
    public const TYPE_INT = '__int@';

    /**
     * 浮点数类型标识符.
    */
    public const TYPE_FLOAT = '__float@';

    /**
     * 字符串类型标识符.
    */
    public const TYPE_STRING = '__string@';

    /**
     * 校验.
     *
     * @throws \InvalidArgumentException
     */
    public function validate(mixed $value, array $param, IValidator $validator, string $field): bool
    {
        if (!array_key_exists(0, $param)) {
            $e = 'Missing the first element of param.';

            throw new InvalidArgumentException($e);
        }

        if (!is_string($param[0]) && !is_object($param[0])) {
            return false;
        }

        $select = $this->normalizeSelect($value, $param, $field);
        $this->parseExceptId($select, $param);
        $this->parseAdditional($select, $param);

        return 0 === $select->findCount();
    }

    /**
     * 创建语法规则.
     *
     * @throws \InvalidArgumentException
     */
    public static function rule(string $entity, ?string $field = null, mixed $exceptId = null, ?string $primaryKey = null, ...$additional): string
    {
        if (!arr($additional, ['scalar'])) {
            $e = 'Unique additional conditions must be scalar type.';

            throw new InvalidArgumentException($e);
        }

        $tmp = [];
        $tmp[] = $entity;
        $tmp[] = $field ?: self::PLACEHOLDER;
        $tmp[] = $exceptId && self::PLACEHOLDER !== $exceptId ? self::encodeConditionValue($exceptId) : self::PLACEHOLDER;
        $tmp[] = $primaryKey ?: self::PLACEHOLDER;
        foreach ($additional as $key => &$value) {
            if (1 === $key % 2) {
                $value = self::encodeConditionValue($value);
            }
        }
        $tmp = array_merge($tmp, $additional);

        return 'unique:'.implode(',', $tmp);
    }

    /**
     * 取得查询.
     */
    protected function normalizeSelect(mixed $value, array $param, string $field): Select
    {
        $entity = $this->parseEntity($param);

        if (isset($param[1]) && self::PLACEHOLDER !== $param[1]) {
            $field = $param[1];
        }

        $value = self::decodeConditionValue($value);

        if (false !== strpos($field, self::SEPARATE)) {
            $select = $entity->select()->databaseSelect();
            foreach (explode(self::SEPARATE, $field) as $v) {
                $select->where($v, $value);
            }
        } else {
            $select = $entity->select()->where($field, $value);
        }

        return $select;
    }

    /**
     * 分析实体.
     *
     * @throws \InvalidArgumentException
     */
    protected function parseEntity(array $param): Entity
    {
        if (is_string($param[0])) {
            if (!class_exists($param[0])) {
                $e = sprintf('Validate entity `%s` was not found.', $param[0]);

                throw new InvalidArgumentException($e);
            }

            $entity = new $param[0]();
        } else {
            $entity = $param[0];
        }

        /** @var \Leevel\Database\Ddd\Entity $entity */
        if (!$entity instanceof Entity) {
            $e = sprintf('Validate entity `%s` must be an entity.', $entity::class);

            throw new InvalidArgumentException($e);
        }

        return $entity;
    }

    /**
     * 排除主键.
     */
    protected function parseExceptId(Select $select, array $param): void
    {
        if (isset($param[2]) && self::PLACEHOLDER !== $param[2]) {
            $withoutPrimary = true;
            $primaryKey = null;
            if (!empty($param[3]) && self::PLACEHOLDER !== $param[3]) {
                $primaryKey = $param[3];
            } else {
                if (1 === count($primaryKey = $select->entity()->primaryKey())) {
                    $primaryKey = reset($primaryKey);
                } else {
                    $withoutPrimary = false;
                }
            }
            if ($withoutPrimary) {
                $select->where($primaryKey, '<>', self::decodeConditionValue($param[2]));
            }
        }
    }

    /**
     * 额外条件.
     *
     * @throws \InvalidArgumentException
     */
    protected function parseAdditional(Select $select, array $param): void
    {
        if (($num = count($param)) >= 4) {
            for ($i = 4; $i < $num; $i += 2) {
                if (!isset($param[$i + 1])) {
                    $e = 'Unique additional conditions must be paired.';

                    throw new InvalidArgumentException($e);
                }

                if (false !== strpos($param[$i], self::SEPARATE)) {
                    list($field, $operator) = explode(self::SEPARATE, $param[$i]);
                } else {
                    $field = $param[$i];
                    $operator = '=';
                }

                $select->where($field, $operator, self::decodeConditionValue($param[$i + 1]));
            }
        }
    }

    /**
     * 解码查询条件值.
     */
    protected static function decodeConditionValue(mixed $value): float|int|string
    {
        if (!is_string($value)) {
            return $value;
        }

        if (0 === strpos($value, self::TYPE_STRING)) {
            return (string) substr($value, strlen(self::TYPE_STRING));
        }

        if (0 === strpos($value, self::TYPE_FLOAT)) {
            return (float) substr($value, strlen(self::TYPE_FLOAT));
        }

        if (0 === strpos($value, self::TYPE_INT)) {
            return (int) substr($value, strlen(self::TYPE_INT));
        }

        return $value;
    }

    /**
     * 编码查询条件值.
     */
    protected static function encodeConditionValue(mixed $value): string
    {
        if (is_int($value)) {
            return self::TYPE_INT.$value;
        }

        if (is_float($value)) {
            return self::TYPE_FLOAT.$value;
        }

        return self::TYPE_STRING.$value;
    }
}

// import fn.
class_exists(arr::class);
