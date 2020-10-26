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
use InvalidArgumentException;

/**
 * 规约链式表达式实现.
 */
class Specification implements ISpecification
{
    /**
     * 闭包规约判断.
     *
     * @var \Closure
     */
    protected ?Closure $spec = null;

    /**
     * 闭包规约实现.
     *
     * @var \Closure
     */
    protected ?Closure $handle = null;

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
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public static function make(Closure $spec, Closure $handle): ISpecification
    {
        return new static($spec, $handle);
    }

    /**
     * 转换为标准规约.
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public static function from(ISpecification $specification): ISpecification
    {
        return new static(
            Closure::fromCallable([$specification, 'isSatisfiedBy']),
            Closure::fromCallable([$specification, 'handle']),
        );
    }

    /**
     * 是否满足规约.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     */
    public function isSatisfiedBy(Entity $entity): bool
    {
        $spec = $this->spec;

        return $spec($entity);
    }

    /**
     * 规约实现.
     *
     * @param \Leevel\Database\Ddd\Select $select
     * @param \Leevel\Database\Ddd\Entity $entity
     */
    public function handle(Select $select, Entity $entity): void
    {
        $handle = $this->handle;
        $handle($select, $entity);
    }

    /**
     * 规约与操作.
     *
     * @param \Closure|\Leevel\Database\Ddd\ISpecification $spec
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function and(Closure|ISpecification $spec, ?Closure $handle = null): ISpecification
    {
        $spec = $this->normalizeSpecification($spec, $handle);
        $this->validateIsStandard();
        $oldSpec = $this->spec;
        $oldHandle = $this->handle;

        $this->spec = function (Entity $entity) use ($oldSpec, $spec): bool {
            return $oldSpec($entity) && $spec->isSatisfiedBy($entity);
        };

        $this->handle = function (Select $select, Entity $entity) use ($spec, $oldHandle) {
            $oldHandle($select, $entity);
            $spec->handle($select, $entity);
        };

        return $this;
    }

    /**
     * 规约或操作.
     *
     * @param \Closure|\Leevel\Database\Ddd\ISpecification $spec
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function or(Closure|ISpecification $spec, ?Closure $handle = null): ISpecification
    {
        $spec = $this->normalizeSpecification($spec, $handle);
        $this->validateIsStandard();
        $oldSpec = $this->spec;
        $oldHandle = $this->handle;

        $this->spec = function (Entity $entity): bool {
            return true;
        };

        $this->handle = function (Select $select, Entity $entity) use ($oldSpec, $spec, $oldHandle) {
            if ($oldSpec($entity)) {
                $oldHandle($select, $entity);
            } elseif ($spec->isSatisfiedBy($entity)) {
                $spec->handle($select, $entity);
            }
        };

        return $this;
    }

    /**
     * 规约反操作.
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    public function not(): ISpecification
    {
        $this->validateIsStandard();
        $oldSpec = $this->spec;
        $this->spec = function (Entity $entity) use ($oldSpec): bool {
            return !$oldSpec($entity);
        };

        return $this;
    }

    /**
     * 整理规约.
     *
     * @param \Closure|\Leevel\Database\Ddd\ISpecification $spec
     *
     * @return \Leevel\Database\Ddd\ISpecification
     */
    protected function normalizeSpecification(Closure|ISpecification $spec, ?Closure $handle = null): ISpecification
    {
        if (!($spec instanceof ISpecification)) {
            $spec = self::make($spec, $handle);
        }

        return $spec;
    }

    /**
     * 校验是否为标准规约.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateIsStandard(): void
    {
        if (!$this->spec || !$this->handle) {
            $e = sprintf('Non standard specification,please use \%s::from(\%s $specification) to convert it.', self::class, ISpecification::class);

            throw new InvalidArgumentException($e);
        }
    }
}
