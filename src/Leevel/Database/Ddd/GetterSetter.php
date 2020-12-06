<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

/**
 * 实体 Getter Setter.
 */
trait GetterSetter
{
    use Connect;

    /**
     * Prop data.
     */
    private array $data = [];

    /**
     * Setter.
     */
    public function setter(string $prop, mixed $value): Entity
    {
        $this->data[$this->realProp($prop)] = $value;

        return $this;
    }

    /**
     * Getter.
     */
    public function getter(string $prop): mixed
    {
        return $this->data[$this->realProp($prop)] ?? null;
    }
}
