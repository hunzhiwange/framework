<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

use Closure;

/**
 * 规约接口.
 */
interface ISpecification
{
    /**
     * 创建规约表达式.
     */
    public static function make(Closure $spec, Closure $handle): static;

    /**
     * 转换为标准规约.
     */
    public static function from(self $specification): static;

    /**
     * 是否满足规约.
     */
    public function isSatisfiedBy(Entity $entity): bool;

    /**
     * 规约实现.
     */
    public function handle(Select $select, Entity $entity): void;

    /**
     * 规约与操作.
     */
    public function and(Closure|ISpecification $spec, ?Closure $handle = null): self;

    /**
     * 规约或操作.
     */
    public function or(Closure|ISpecification $spec, ?Closure $handle = null): self;

    /**
     * 规约反操作.
     */
    public function not(): self;
}
