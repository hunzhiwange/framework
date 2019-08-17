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

use Closure;

/**
 * 规约实现.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.29
 *
 * @version 1.0
 */
class Specification implements ISpecification
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
     *
     * @param \Closure $spec
     * @param \Closure $handle
     */
    public function __construct(Closure $spec, Closure $handle)
    {
        $this->spec = $spec;
        $this->handle = $handle;
    }

    /**
     * 创建规约.
     *
     * @param \Closure $spec
     * @param \Closure $handle
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
     *
     * @return bool
     */
    public function isSatisfiedBy(IEntity $entity): bool
    {
        return call_user_func($this->spec, $entity);
    }

    /**
     * 规约实现.
     *
     * @param \Leevel\Database\Ddd\Select  $select
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    public function handle(Select $select, IEntity $entity): void
    {
        call_user_func($this->handle, $select, $entity);
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
        return new self(function (IEntity $entity) use ($spec): bool {
            return $this->isSatisfiedBy($entity) && $spec->isSatisfiedBy($entity);
        }, function (Select $select, IEntity $entity) use ($spec) {
            $this->handle($select, $entity);
            $spec->handle($select, $entity);
        });
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
        return new self(function (IEntity $entity): bool {
            return true;
        }, function (Select $select, IEntity $entity) use ($spec) {
            if ($this->isSatisfiedBy($entity)) {
                $this->handle($select, $entity);
            } elseif ($spec->isSatisfiedBy($entity)) {
                $spec->handle($select, $entity);
            }
        });
    }

    /**
     * 规约 Not 操作.
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function not(): ISpecification
    {
        return new self(function (IEntity $entity): bool {
            return !$this->isSatisfiedBy($entity);
        }, $this->handle);
    }
}
