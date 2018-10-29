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

namespace Leevel\Database\Ddd;

use Closure;

/**
 * 规约链式表达式接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.29
 *
 * @version 1.0
 */
interface ISpecificationExpression
{
    /**
     * 是否满足规约.
     *
     * @param mixed $candidate
     *
     * @return bool
     */
    public function isSatisfiedBy($candidate): bool;

    /**
     * 闭包规约 And 操作.
     *
     * @param \Closure $spec
     *
     * @return \Leevel\Database\Ddd\ISpecificationExpression
     */
    public function and(Closure $spec): self;

    /**
     * 闭包规约 Or 操作.
     *
     * @param \Closure $spec
     *
     * @return \Leevel\Database\Ddd\ISpecificationExpression
     */
    public function or(Closure $spec): self;

    /**
     * 闭包规约 not 操作.
     *
     * @param \Closure $spec
     *
     * @return \Leevel\Database\Ddd\ISpecificationExpression
     */
    public function not(): self;

    /**
     * 规约 And 操作.
     *
     * @param \Leevel\Database\Ddd\ISpecification $spec
     *
     * @return \Leevel\Database\Ddd\ISpecificationExpression
     */
    public function andSpec(ISpecification $spec): self;

    /**
     * 规约 Or 操作.
     *
     * @param \Leevel\Database\Ddd\ISpecification $spec
     *
     * @return \Leevel\Database\Ddd\ISpecificationExpression
     */
    public function orSpec(ISpecification $spec): self;

    /**
     * 规约 Not 操作.
     *
     * @param \Leevel\Database\Ddd\ISpecification $spec
     *
     * @return \Leevel\Database\Ddd\ISpecificationExpression
     */
    public function notSpec(): self;
}
