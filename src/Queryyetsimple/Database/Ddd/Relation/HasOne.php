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

/**
 * 关联模型 HasOne.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.28
 *
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
     * 匹配关联查询数据到模型.
     *
     * @param \Leevel\Database\Ddd\IModel[] $arrModel
     * @param \leevel\collection            $objResult
     * @param string                        $strRelation
     *
     * @return array
     */
    public function matchPreLoad(array $arrModel, collection $objResult, $strRelation)
    {
        return $this->matchPreLoadOneOrMany($arrModel, $objResult, $strRelation, 'one');
    }
}
