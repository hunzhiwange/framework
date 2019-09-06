<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Validate;

use InvalidArgumentException;
use Leevel\Database\Ddd\IEntity;
use Leevel\Database\Ddd\Select;
use function Leevel\Support\Type\type_array;
use Leevel\Support\Type\type_array;

/**
 * 不能重复值验证规则.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.11.26
 *
 * @version 1.0
 */
class UniqueRule
{
    /**
     * 占位符.
     *
     * @var string
     */
    const PLACEHOLDER = '_';

    /**
     * 隔离符.
     *
     * @var string
     */
    const SEPARATE = ':';

    /**
     * 整型类型标识符.
     *
     * @var string
     */
    const TYPE_INT = '__int@';

    /**
     * 浮点数类型标识符.
     *
     * @var string
     */
    const TYPE_FLOAT = '__float@';

    /**
     * 校验.
     *
     * @param mixed                       $value
     * @param array                       $param
     * @param \Leevel\Validate\IValidator $validator
     * @param string                      $field
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function validate($value, array $param, IValidator $validator, string $field): bool
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
     * @param string      $entity
     * @param null|string $field
     * @param null|mixed  $exceptId
     * @param null|string $primaryKey
     * @param array       ...$additional
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function rule(string $entity, ?string $field = null, $exceptId = null, ?string $primaryKey = null, ...$additional): string
    {
        if (!type_array($additional, ['scalar'])) {
            $e = 'Unique additional conditions must be scalar type.';

            throw new InvalidArgumentException($e);
        }

        $tmp = [];
        $tmp[] = $entity;
        $tmp[] = $field ?: self::PLACEHOLDER;
        $tmp[] = $exceptId ? self::encodeConditionValue($exceptId) : self::PLACEHOLDER;
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
     *
     * @param array  $param
     * @param mixed  $value
     * @param string $field
     *
     * @return \Leevel\Database\Ddd\Select
     */
    protected function normalizeSelect($value, array $param, string $field): Select
    {
        $entity = $this->parseEntity($param);

        if (isset($param[1]) && self::PLACEHOLDER !== $param[1]) {
            $field = $param[1];
        }

        $value = self::decodeConditionValue($value);

        if (false !== strpos($field, self::SEPARATE)) {
            $select = $entity->select()->selfDatabaseSelect();
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
     * @param array $param
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    protected function parseEntity(array $param): IEntity
    {
        $connect = null;

        if (is_string($param[0])) {
            if (false !== strpos($param[0], self::SEPARATE)) {
                list($connect, $entityClass) = explode(self::SEPARATE, $param[0]);
            } else {
                $entityClass = $param[0];
            }

            if (!class_exists($entityClass)) {
                $e = sprintf('Validate entity `%s` was not found.', $entityClass);

                throw new InvalidArgumentException($e);
            }

            $entity = new $entityClass();
        } else {
            $entity = $param[0];
        }

        /** @var \Leevel\Database\Ddd\IEntity $entity */
        if (!($entity instanceof IEntity)) {
            $e = sprintf('Validate entity `%s` must be an entity.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        if ($connect) {
            $entity->withConnect($connect);
        }

        return $entity;
    }

    /**
     * 排除主键.
     *
     * @param \Leevel\Database\Ddd\Select $select
     * @param array                       $param
     */
    protected function parseExceptId(Select $select, array $param): void
    {
        if (isset($param[2]) && self::PLACEHOLDER !== $param[2]) {
            $withoutPrimary = true;

            if (!empty($param[3]) && self::PLACEHOLDER !== $param[3]) {
                $primaryKey = $param[3];
            } else {
                if (is_string($_ = $select->entity()->primaryKey())) {
                    $primaryKey = $_;
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
     * @param \Leevel\Database\Ddd\Select $select
     * @param array                       $param
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
     *
     * @param mixed $value
     *
     * @return int|string
     */
    protected static function decodeConditionValue($value)
    {
        if (!is_string($value)) {
            return $value;
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
     *
     * @param mixed $value
     *
     * @return string
     */
    protected static function encodeConditionValue($value): string
    {
        if (is_int($value)) {
            return self::TYPE_INT.$value;
        }

        if (is_float($value)) {
            return self::TYPE_FLOAT.$value;
        }

        return $value;
    }
}

// import fn.
class_exists(type_array::class);
