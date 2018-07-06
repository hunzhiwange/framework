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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Database\Ddd\Relation;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\IEntity;

/**
 * 关联模型实体 BelongsTo.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.28
 *
 * @version 1.0
 */
class BelongsTo extends Relation
{
    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Ddd\IEntity $objTargetEntity
     * @param \Leevel\Database\Ddd\IEntity $objSourceEntity
     * @param string                       $strTargetKey
     * @param string                       $strSourceKey
     */
    public function __construct(IEntity $objTargetEntity, IEntity $objSourceEntity, $strTargetKey, $strSourceKey)
    {
        parent::__construct($objTargetEntity, $objSourceEntity, $strTargetKey, $strSourceKey);
    }

    /**
     * 关联基础查询条件.
     */
    public function addRelationCondition()
    {
        if (static::$booRelationCondition) {
            $this->objSelect->where($this->strTargetKey, $this->getSourceValue());
        }
    }

    /**
     * 匹配关联查询数据到模型实体.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $arrEntity
     * @param \Leevel\Collection\Collection  $objResult
     * @param string                         $strRelation
     *
     * @return array
     */
    public function matchPreLoad(array $arrEntity, collection $objResult, $strRelation)
    {
        $arrMap = $this->buildMap($objResult);

        foreach ($arrEntity as &$objEntity) {
            $mixKey = $objEntity->getProp($this->strSourceKey);
            if (isset($arrMap[$mixKey])) {
                $objEntity->setRelationProp($strRelation, $arrMap[$mixKey]);
            }
        }

        return $arrEntity;
    }

    /**
     * 设置预载入关联查询条件.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $arrEntity
     */
    public function preLoadCondition(array $arrEntity)
    {
        $this->objSelect->whereIn($this->strTargetKey, $this->getPreLoadEntityValue($arrEntity));
    }

    /**
     * 取回源模型实体对应数据.
     *
     * @return mixed
     */
    public function getSourceValue()
    {
        return $this->objSourceEntity->getProp($this->strSourceKey);
    }

    /**
     * 查询关联对象
     *
     * @return mixed
     */
    public function sourceQuery()
    {
        return $this->objSelect->getOne();
    }

    /**
     * 模型实体隐射数据.
     *
     * @param \Leevel\Collection\Collection $objResult
     *
     * @return array
     */
    protected function buildMap(collection $objResult)
    {
        $arrMap = [];

        foreach ($objResult as $objResultEntity) {
            $arrMap[$objResultEntity->getProp($this->strTargetKey)] = $objResultEntity;
        }

        return $arrMap;
    }

    /**
     * 分析预载入模型实体中对应的源数据.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $arrEntity
     *
     * @return array
     */
    protected function getPreLoadEntityValue(array $arrEntity)
    {
        $arr = [];

        foreach ($arrEntity as $objEntity) {
            if (null !== ($mixTemp = $objEntity->getProp($this->strSourceKey))) {
                $arr[] = $mixTemp;
            }
        }

        if (0 === count($arr)) {
            return [
                0,
            ];
        }

        return array_values(array_unique($arr));
    }
}
