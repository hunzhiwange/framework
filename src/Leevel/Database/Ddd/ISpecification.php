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

namespace Leevel\Database\Ddd;

/**
 * 规约接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.29
 *
 * @version 1.0
 */
interface ISpecification
{
    /**
     * 是否满足规约.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return bool
     */
    public function isSatisfiedBy(IEntity $entity): bool;

    /**
     * 规约实现.
     *
     * @param \Leevel\Database\Ddd\Select  $select
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    public function handle(Select $select, IEntity $entity);

    /**
     * 规约 And 操作.
     *
     * @param \Leevel\Database\Ddd\ISpecification $spec
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function and(self $spec): self;

    /**
     * 规约 Or 操作.
     *
     * @param \Leevel\Database\Ddd\ISpecification $spec
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function or(self $spec): self;

    /**
     * 规约 Not 操作.
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function not(): self;
}
