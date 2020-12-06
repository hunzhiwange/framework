<?php

declare(strict_types=1);

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
    */
    protected ?Closure $spec = null;

    /**
     * 闭包规约实现.
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
     * {@inheritDoc}
     */
    public static function make(Closure $spec, Closure $handle): static
    {
        return new static($spec, $handle);
    }

    /**
     * {@inheritDoc}
     */
    public static function from(ISpecification $specification): static
    {
        return new static(
            Closure::fromCallable([$specification, 'isSatisfiedBy']),
            Closure::fromCallable([$specification, 'handle']),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function isSatisfiedBy(Entity $entity): bool
    {
        $spec = $this->spec;

        return $spec($entity);
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Select $select, Entity $entity): void
    {
        $handle = $this->handle;
        $handle($select, $entity);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
