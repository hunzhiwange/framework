<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\mvc\relation;

use queryyetsimple\{
    mvc\imodel,
    support\collection
};

/**
 * 关联模型 belongs_to
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.28
 * @version 1.0
 */
class belongs_to extends relation
{

    /**
     * 构造函数
     *
     * @param \queryyetsimple\mvc\imodel $objTargetModel
     * @param \queryyetsimple\mvc\imodel $objSourceModel
     * @param string $strTargetKey
     * @param string $strSourceKey
     * @return void
     */
    public function __construct(imodel $objTargetModel, imodel $objSourceModel, $strTargetKey, $strSourceKey)
    {
        parent::__construct($objTargetModel, $objSourceModel, $strTargetKey, $strSourceKey);
    }

    /**
     * 关联基础查询条件
     *
     * @return void
     */
    public function addRelationCondition()
    {
        if (static::$booRelationCondition) {
            $this->objSelect->where($this->strTargetKey, $this->getSourceValue());
        }
    }

    /**
     * 匹配关联查询数据到模型
     *
     * @param \queryyetsimple\mvc\imodel[] $arrModel
     * @param \queryyetsimple\support\collection $objResult
     * @param string $strRelation
     * @return array
     */
    public function matchPreLoad(array $arrModel, collection $objResult, $strRelation)
    {
        $arrMap = $this->buildMap($objResult);

        foreach ($arrModel as &$objModel) {
            $mixKey = $objModel->getProp($this->strSourceKey);
            if (isset($arrMap[$mixKey])) {
                $objModel->setRelationProp($strRelation, $arrMap[$mixKey]);
            }
        }

        return $arrModel;
    }

    /**
     * 设置预载入关联查询条件
     *
     * @param \queryyetsimple\mvc\imodel[] $arrModel
     * @return void
     */
    public function preLoadCondition(array $arrModel)
    {
        $this->objSelect->whereIn($this->strTargetKey, $this->getPreLoadModelValue($arrModel));
    }

    /**
     * 取回源模型对应数据
     *
     * @return mixed
     */
    public function getSourceValue()
    {
        return $this->objSourceModel->getProp($this->strSourceKey);
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
     * 模型隐射数据
     *
     * @param \queryyetsimple\support\collection $objResult
     * @return array
     */
    protected function buildMap(collection $objResult)
    {
        $arrMap = [];

        foreach ($objResult as $objResultModel) {
            $arrMap[$objResultModel->getProp($this->strTargetKey)] = $objResultModel;
        }

        return $arrMap;
    }

    /**
     * 分析预载入模型中对应的源数据
     *
     * @param \queryyetsimple\mvc\imodel[] $arrModel
     * @return array
     */
    protected function getPreLoadModelValue(array $arrModel)
    {
        $arr = [];

        foreach ($arrModel as $objModel) {
            if (! is_null($mixTemp = $objModel->getProp($this->strSourceKey))) {
                $arr[] = $mixTemp;
            }
        }

        if (count($arr) == 0) {
            return [
                0
            ];
        }

        return array_values(array_unique($arr));
    }
}
