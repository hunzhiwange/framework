<?php

declare(strict_types=1);

namespace Leevel\Validate;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Select;
use Leevel\Support\Type\Arr;
use Leevel\Support\Type\StringEncode;

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
     * 校验.
     *
     * @throws \InvalidArgumentException
     */
    public function handle(mixed $value, array $param, IValidator $validator, string $field): bool
    {
        if (!\array_key_exists(0, $param)) {
            $e = 'Missing the first element of param.';

            throw new \InvalidArgumentException($e);
        }

        if (!\is_string($param[0]) && !\is_object($param[0])) {
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
    public static function rule(string $entity, ?string $field = null, mixed $exceptId = null, ?string $primaryKey = null, array $additional = []): string
    {
        if (!Arr::handle($additional, ['string:scalar'])) {
            $e = 'Unique additional conditions must be `string:scalar` array.';

            throw new \InvalidArgumentException($e);
        }

        $tmp = [];
        $tmp[] = $entity;
        $tmp[] = $field ?: self::PLACEHOLDER;
        // @phpstan-ignore-next-line
        $tmp[] = $exceptId && self::PLACEHOLDER !== $exceptId ? self::encodeConditionValue($exceptId) : self::PLACEHOLDER;
        $tmp[] = $primaryKey ?: self::PLACEHOLDER;
        foreach ($additional as $key => $value) {
            $tmp[] = $key;
            $tmp[] = self::encodeConditionValue($value);
        }

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

        if (str_contains($field, self::SEPARATE)) {
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
        if (\is_string($param[0])) {
            if (!class_exists($param[0])) {
                $e = sprintf('Validate entity `%s` was not found.', $param[0]);

                throw new \InvalidArgumentException($e);
            }

            $entity = new $param[0]();
        } else {
            $entity = $param[0];
        }

        /** @var \Leevel\Database\Ddd\Entity $entity */
        if (!$entity instanceof Entity) {
            $e = sprintf('Validate entity `%s` must be an entity.', $entity::class);

            throw new \InvalidArgumentException($e);
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
                if (1 === \count($primaryKey = $select->entity()->primaryKey())) {
                    $primaryKey = reset($primaryKey);
                } else {
                    $withoutPrimary = false;
                }
            }
            if ($withoutPrimary) {
                $select->where($primaryKey, '<>', $param[2]);
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
        if (($num = \count($param)) >= 4) {
            for ($i = 4; $i < $num; $i += 2) {
                if (!isset($param[$i + 1])) {
                    $e = 'Unique additional conditions must be paired.';

                    throw new \InvalidArgumentException($e);
                }

                if (str_contains($param[$i], self::SEPARATE)) {
                    [$field, $operator] = explode(self::SEPARATE, $param[$i]);
                } else {
                    $field = $param[$i];
                    $operator = '=';
                }

                $select->where($field, $operator, $param[$i + 1]);
            }
        }
    }

    /**
     * 编码查询条件值.
     */
    protected static function encodeConditionValue(string|int|float $value): string
    {
        return StringEncode::handle($value, false);
    }
}
