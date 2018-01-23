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
namespace Queryyetsimple\Mvc\Relation;

use Queryyetsimple\Collection\Collection;

/**
 * 关联模型 HasOne
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.28
 * @version 1.0
 */
class HasOne extends HasMany
{

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
     * 匹配关联查询数据到模型
     *
     * @param \Queryyetsimple\Mvc\IModel[] $arrModel
     * @param \queryyetsimple\collection $objResult
     * @param string $strRelation
     * @return array
     */
    public function matchPreLoad(array $arrModel, collection $objResult, $strRelation)
    {
        return $this->matchPreLoadOneOrMany($arrModel, $objResult, $strRelation, 'one');
    }
}
