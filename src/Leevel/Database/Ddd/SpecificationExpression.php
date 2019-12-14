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
 * 规约链式表达式实现.
 */
class SpecificationExpression implements ISpecification
{
    /**
     * 闭包规约判断.
     *
     * @var \Closure
     */
    protected Closure $spec;

    /**
     * 闭包规约实现.
     *
     * @var \Closure
     */
    protected Closure $handle;

    /**
     * 构造函数.
     */
    public function __construct(Closure $spec, Closure $handle)
    {
        $this->spec = $spec;
        $this->handle = $handle;
    }

    /**
     * 创建规约表达式.
     *
     * @return static
     */
    public static function make(Closure $spec, Closure $handle): ISpecification
    {
        return new static($spec, $handle);
    }

    /**
     * 是否满足规约.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    public function isSatisfiedBy(IEntity $entity): bool
    {
        $spec = $this->spec;

        return $spec($entity);
    }

    /**
     * 规约实现.
     *
     * @param \Leevel\Database\Ddd\Select  $select
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    public function handle(Select $select, IEntity $entity): void
    {
        $handle = $this->handle;
        $handle($select, $entity);
    }

    /**
     * 规约 And 操作.
     *
     * @param \Leevel\Database\Ddd\ISpecification $spec
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function and(ISpecification $spec): ISpecification
    {
        $old = $this->spec;
        $oldHandle = $this->handle;

        $this->spec = function (IEntity $entity) use ($old, $spec): bool {
            return $old($entity) && $spec->isSatisfiedBy($entity);
        };

        $this->handle = function (Select $select, IEntity $entity) use ($spec, $oldHandle) {
            $oldHandle($select, $entity);
            $spec->handle($select, $entity);
        };

        return $this;
    }

    /**
     * 规约 Or 操作.
     *
     * @param \Leevel\Database\Ddd\ISpecification $spec
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function or(ISpecification $spec): ISpecification
    {
        $old = $this->spec;
        $oldHandle = $this->handle;

        $this->spec = function (IEntity $entity): bool {
            return true;
        };

        $this->handle = function (Select $select, IEntity $entity) use ($old, $spec, $oldHandle) {
            if ($old($entity)) {
                $oldHandle($select, $entity);
            } elseif ($spec->isSatisfiedBy($entity)) {
                $spec->handle($select, $entity);
            }
        };

        return $this;
    }

    /**
     * 规约 Not 操作.
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function not(): ISpecification
    {
        $old = $this->spec;

        $this->spec = function (IEntity $entity) use ($old): bool {
            return !$old($entity);
        };

        return $this;
    }

    /**
     * 闭包规约 And 操作.
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function andClosure(Closure $spec, Closure $handle): ISpecification
    {
        $old = $this->spec;
        $oldHandle = $this->handle;

        $this->spec = function (IEntity $entity) use ($old, $spec): bool {
            return $old($entity) && $spec($entity);
        };

        $this->handle = function (Select $select, IEntity $entity) use ($oldHandle, $handle) {
            $oldHandle($select, $entity);
            $handle($select, $entity);
        };

        return $this;
    }

    /**
     * 闭包规约 Or 操作.
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function orClosure(Closure $spec, Closure $handle): ISpecification
    {
        $old = $this->spec;
        $oldHandle = $this->handle;

        $this->spec = function (IEntity $entity): bool {
            return true;
        };

        $this->handle = function (Select $select, IEntity $entity) use ($old, $spec, $oldHandle, $handle) {
            if ($old($entity)) {
                $oldHandle($select, $entity);
            } elseif ($spec($entity)) {
                $handle($select, $entity);
            }
        };

        return $this;
    }
}
