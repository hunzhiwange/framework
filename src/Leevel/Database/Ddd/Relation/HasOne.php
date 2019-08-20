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

namespace Leevel\Database\Ddd\Relation;

use Leevel\Collection\Collection;

/**
 * 关联模型实体 HasOne.
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
        if (true === $this->emptySourceData) {
            return $this->targetEntity->make();
        }

        return $this->select->findOne();
    }

    /**
     * 匹配关联查询数据到模型实体.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     * @param \Leevel\Collection\Collection  $result
     * @param string                         $relation
     *
     * @return array
     */
    public function matchPreLoad(array $entitys, Collection $result, string $relation): array
    {
        return $this->matchPreLoadOneOrMany($entitys, $result, $relation, 'one');
    }
}
