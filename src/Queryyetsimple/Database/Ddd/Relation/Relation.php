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

use Closure;
use Exception;
use Leevel\Collection\Collection;
use Leevel\Database\Ddd\IModel;

/**
 * 关联模型基类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.28
 *
 * @version 1.0
 */
abstract class Relation
{
    /**
     * 查询对象
     *
     * @var \Leevel\Database\Select
     */
    protected $objSelect;

    /**
     * 关联目标模型.
     *
     * @var \Leevel\Database\Ddd\IModel
     */
    protected $objTargetModel;

    /**
     * 源模型.
     *
     * @var \Leevel\Database\Ddd\IModel
     */
    protected $objSourceModel;

    /**
     * 目标关联字段.
     *
     * @var string
     */
    protected $strTargetKey;

    /**
     * 源关联字段.
     *
     * @var string
     */
    protected $strSourceKey;

    /**
     * 是否初始化查询.
     *
     * @var bool
     */
    protected static $booRelationCondition = true;

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Ddd\IModel $objTargetModel
     * @param \Leevel\Database\Ddd\IModel $objSourceModel
     * @param string                      $strTargetKey
     * @param string                      $strSourceKey
     */
    public function __construct(IModel $objTargetModel, IModel $objSourceModel, $strTargetKey, $strSourceKey)
    {
        $this->objTargetModel = $objTargetModel;
        $this->objSourceModel = $objSourceModel;
        $this->strTargetKey = $strTargetKey;
        $this->strSourceKey = $strSourceKey;

        $this->getSelectFromModel();
        $this->addRelationCondition();
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $arrArgs
     *
     * @return mixed
     */
    public function __call(string $method, array $arrArgs)
    {
        $objSelect = $this->objSelect->{$method}(...$arrArgs);

        if ($this->getSelect() === $objSelect) {
            return $this;
        }

        return $objSelect;
    }

    /**
     * 返回查询.
     *
     * @return \Leevel\Database\Select
     */
    public function getSelect()
    {
        return $this->objSelect;
    }

    /**
     * 取得预载入关联模型.
     *
     * @return \Leevel\Collection\Collection
     */
    public function getPreLoad()
    {
        return $this->querySelelct()->preLoadResult($this->getAll());
    }

    /**
     * 取得关联目标模型.
     *
     * @return \Leevel\Database\Ddd\IModel
     */
    public function getTargetModel()
    {
        return $this->objTargetModel;
    }

    /**
     * 取得源模型.
     *
     * @return \Leevel\Database\Ddd\IModel
     */
    public function getSourceModel()
    {
        return $this->objSourceModel;
    }

    /**
     * 取得目标字段.
     *
     * @return string
     */
    public function getTargetKey()
    {
        return $this->strTargetKey;
    }

    /**
     * 取得源字段.
     *
     * @return string
     */
    public function getSourceKey()
    {
        return $this->strSourceKey;
    }

    /**
     * 获取不带关联条件的关联对象
     *
     * @param \Closure $calReturnRelation
     *
     * @return \leevel\Mvc\Relation\Relation
     */
    public static function withoutRelationCondition(Closure $calReturnRelation)
    {
        $booOld = static::$booRelationCondition;
        static::$booRelationCondition = false;

        $objRelation = call_user_func($calReturnRelation);
        if (!($objRelation instanceof self)) {
            throw new Exception('The result must be relation.');
        }

        static::$booRelationCondition = $booOld;

        return $objRelation;
    }

    /**
     * 关联基础查询条件.
     */
    abstract public function addRelationCondition();

    /**
     * 设置预载入关联查询条件.
     *
     * @param \Leevel\Database\Ddd\IModel[] $arrModel
     */
    abstract public function preLoadCondition(array $arrModel);

    /**
     * 匹配关联查询数据到模型 HasMany.
     *
     * @param \Leevel\Database\Ddd\IModel[] $arrModel
     * @param \Leevel\Collection\Collection $objResult
     * @param string                        $strRelation
     *
     * @return array
     */
    abstract public function matchPreLoad(array $arrModel, collection $objResult, $strRelation);

    /**
     * 查询关联对象
     *
     * @return mixed
     */
    abstract public function sourceQuery();

    /**
     * 返回模型的主键.
     *
     * @param \Leevel\Database\Ddd\IModel[] $arrModel
     * @param string                        $strKey
     *
     * @return array
     */
    protected function getModelKey(array $arrModel, $strKey = null)
    {
        return array_unique(array_values(array_map(function ($objModel) use ($strKey) {
            return $strKey ? $objModel->getProp($strKey) : $objModel->getPrimaryKeyForQuery();
        }, $arrModel)));
    }

    /**
     * 从模型返回查询.
     *
     * @return \Leevel\Database\Select
     */
    protected function getSelectFromModel()
    {
        $this->objSelect = $this->objTargetModel->getClassCollectionQuery();
    }
}
