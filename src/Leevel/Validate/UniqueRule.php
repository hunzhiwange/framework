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
use Leevel\Support\Type;

/**
 * 不能重复值验证规则.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.11.26
 *
 * @version 1.0
 */
class UniqueRule extends Rule implements IRule
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
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    public function validate(string $field, $datas, array $parameter): bool
    {
        $this->initArgs($field, $datas, $parameter);

        if (false === $this->validateArgs()) {
            return false;
        }

        $select = $this->normalizeSelect();

        $this->parseExceptId($select);
        $this->parseAdditional($select);

        return 0 === $select->findCount();
    }

    /**
     * 创建语法规则.
     *
     * @param string $entity
     * @param string $field
     * @param mixed  $exceptId
     * @param string $primaryKey
     * @param array  $additional
     *
     * @return string
     */
    public static function rule(string $entity, ?string $field = null, $exceptId = null, ?string $primaryKey = null, ...$additional): string
    {
        if (!Type::arr($additional, ['string'])) {
            throw new InvalidArgumentException('Unique additional conditions must be string.');
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
     * 校验基本参数.
     *
     * @return bool
     */
    protected function validateArgs(): bool
    {
        $this->checkParameterLength($this->field, $this->parameter, 1);

        if (!is_string($this->parameter[0]) && !is_object($this->parameter[0])) {
            return false;
        }

        return true;
    }

    /**
     * 取得查询.
     *
     * @return \Leevel\Database\Ddd\Select
     */
    protected function normalizeSelect(): Select
    {
        $entity = $this->parseEntity();

        $field = $this->field;

        if (isset($this->parameter[1]) && self::PLACEHOLDER !== $this->parameter[1]) {
            $field = $this->parameter[1];
        }

        if (false !== strpos($field, self::SEPARATE)) {
            $select = $entity->selfDatabaseSelect();

            foreach (explode(self::SEPARATE, $field) as $v) {
                $select->where($v, $this->datas);
            }
        } else {
            $select = $entity->where($field, $this->datas);
        }

        return $select;
    }

    /**
     * 分析实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    protected function parseEntity(): IEntity
    {
        $connect = null;

        if (is_string($this->parameter[0])) {
            if (false !== strpos($this->parameter[0], self::SEPARATE)) {
                list($connect, $entityClass) = explode(self::SEPARATE, $this->parameter[0]);
            } else {
                $entityClass = $this->parameter[0];
            }

            if (!class_exists($entityClass)) {
                throw new InvalidArgumentException(
                    sprintf('Validate entity `%s` was not found.', $entityClass)
                );
            }

            $entity = new $entityClass();
        } else {
            $entity = $this->parameter[0];
        }

        if (!($entity instanceof IEntity)) {
            throw new InvalidArgumentException(
                sprintf('Validate entity `%s` must be an entity.', get_class($entity))
            );
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
     */
    protected function parseExceptId(Select $select): void
    {
        if (isset($this->parameter[2]) && self::PLACEHOLDER !== $this->parameter[2]) {
            $withoutPrimary = true;

            if (!empty($this->parameter[3]) && self::PLACEHOLDER !== $this->parameter[3]) {
                $primaryKey = $this->parameter[3];
            } else {
                if (is_string($tmp = $select->entity()->primaryKey())) {
                    $primaryKey = $tmp;
                } else {
                    $withoutPrimary = false;
                }
            }

            if ($withoutPrimary) {
                $select->where($primaryKey, '<>', $this->parameter[2]);
            }
        }
    }

    /**
     * 额外条件.
     *
     * @param \Leevel\Database\Ddd\Select $select
     */
    protected function parseAdditional(Select $select): void
    {
        if (($num = count($this->parameter)) >= 4) {
            for ($i = 4; $i < $num; $i += 2) {
                if (!isset($this->parameter[$i + 1])) {
                    throw new InvalidArgumentException('Unique additional conditions must be paired.');
                }

                if (false !== strpos($this->parameter[$i], self::SEPARATE)) {
                    list($field, $operator) = explode(self::SEPARATE, $this->parameter[$i]);
                } else {
                    $field = $this->parameter[$i];
                    $operator = '=';
                }

                $select->where($field, $operator, $this->parameter[$i + 1]);
            }
        }
    }
}
