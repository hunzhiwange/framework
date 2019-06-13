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
     * 校验.
     *
     * @param mixed                       $value
     * @param array                       $parameter
     * @param \Leevel\Validate\IValidator $validator
     * @param string                      $field
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function validate($value, array $parameter, IValidator $validator, string $field): bool
    {
        if (!array_key_exists(0, $parameter)) {
            $e = 'Missing the first element of parameter.';

            throw new InvalidArgumentException($e);
        }

        if (!is_string($parameter[0]) && !is_object($parameter[0])) {
            return false;
        }

        $select = $this->normalizeSelect($value, $parameter, $field);

        $this->parseExceptId($select, $parameter);
        $this->parseAdditional($select, $parameter);

        return 0 === $select->findCount();
    }

    /**
     * 创建语法规则.
     *
     * @param string $entity
     * @param string $field
     * @param mixed  $exceptId
     * @param string $primaryKey
     * @param array  ...$additional
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function rule(string $entity, ?string $field = null, $exceptId = null, ?string $primaryKey = null, ...$additional): string
    {
        if (!type_array($additional, ['string'])) {
            $e = 'Unique additional conditions must be string.';

            throw new InvalidArgumentException($e);
        }

        $tmp = [];

        $tmp[] = $entity;
        $tmp[] = $field ?: self::PLACEHOLDER;
        $tmp[] = $exceptId ?: self::PLACEHOLDER;
        $tmp[] = $primaryKey ?: self::PLACEHOLDER;

        $tmp = array_merge($tmp, $additional);

        return 'unique:'.implode(',', $tmp);
    }

    /**
     * 取得查询.
     *
     * @param array  $parameter
     * @param mixed  $value
     * @param string $field
     *
     * @return \Leevel\Database\Ddd\Select
     */
    protected function normalizeSelect($value, array $parameter, string $field): Select
    {
        $entity = $this->parseEntity($parameter);

        if (isset($parameter[1]) && self::PLACEHOLDER !== $parameter[1]) {
            $field = $parameter[1];
        }

        if (false !== strpos($field, self::SEPARATE)) {
            $select = $entity->selfDatabaseSelect();

            foreach (explode(self::SEPARATE, $field) as $v) {
                $select->where($v, $value);
            }
        } else {
            $select = $entity->where($field, $value);
        }

        return $select;
    }

    /**
     * 分析实体.
     *
     * @param array $parameter
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    protected function parseEntity(array $parameter): IEntity
    {
        $connect = null;

        if (is_string($parameter[0])) {
            if (false !== strpos($parameter[0], self::SEPARATE)) {
                list($connect, $entityClass) = explode(self::SEPARATE, $parameter[0]);
            } else {
                $entityClass = $parameter[0];
            }

            if (!class_exists($entityClass)) {
                $e = sprintf('Validate entity `%s` was not found.', $entityClass);

                throw new InvalidArgumentException($e);
            }

            $entity = new $entityClass();
        } else {
            $entity = $parameter[0];
        }

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
     * @param array                       $parameter
     */
    protected function parseExceptId(Select $select, array $parameter): void
    {
        if (isset($parameter[2]) && self::PLACEHOLDER !== $parameter[2]) {
            $withoutPrimary = true;

            if (!empty($parameter[3]) && self::PLACEHOLDER !== $parameter[3]) {
                $primaryKey = $parameter[3];
            } else {
                if (is_string($_ = $select->entity()->primaryKey())) {
                    $primaryKey = $_;
                } else {
                    $withoutPrimary = false;
                }
            }

            if ($withoutPrimary) {
                $select->where($primaryKey, '<>', $parameter[2]);
            }
        }
    }

    /**
     * 额外条件.
     *
     * @param \Leevel\Database\Ddd\Select $select
     * @param array                       $parameter
     *
     * @throws \InvalidArgumentException
     */
    protected function parseAdditional(Select $select, array $parameter): void
    {
        if (($num = count($parameter)) >= 4) {
            for ($i = 4; $i < $num; $i += 2) {
                if (!isset($parameter[$i + 1])) {
                    $e = 'Unique additional conditions must be paired.';

                    throw new InvalidArgumentException($e);
                }

                if (false !== strpos($parameter[$i], self::SEPARATE)) {
                    list($field, $operator) = explode(self::SEPARATE, $parameter[$i]);
                } else {
                    $field = $parameter[$i];
                    $operator = '=';
                }

                $select->where($field, $operator, $parameter[$i + 1]);
            }
        }
    }
}

// import fn.
class_exists(type_array::class);
