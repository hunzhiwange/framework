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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Database\Ddd;

use Closure;

/**
 * 规约接口.
 */
interface ISpecification
{
    /**
     * 创建规约表达式.
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public static function make(Closure $spec, Closure $handle): self;

    /**
     * 转换为标准规约.
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public static function from(self $specification): self;

    /**
     * 是否满足规约.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     */
    public function isSatisfiedBy(Entity $entity): bool;

    /**
     * 规约实现.
     *
     * @param \Leevel\Database\Ddd\Select $select
     * @param \Leevel\Database\Ddd\Entity $entity
     */
    public function handle(Select $select, Entity $entity): void;

    /**
     * 规约与操作.
     *
     * @param \Closure|\Leevel\Database\Ddd\ISpecification $spec
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function and(Closure|ISpecification $spec, ?Closure $handle = null): self;

    /**
     * 规约或操作.
     *
     * @param \Closure|\Leevel\Database\Ddd\ISpecification $spec
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function or(Closure|ISpecification $spec, ?Closure $handle = null): self;

    /**
     * 规约反操作.
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function not(): self;
}
