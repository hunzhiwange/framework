<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

/**
 * 实体 Getter Setter 属性风格.
 */
trait GetterSetterProp
{
    use Connect;

    /**
     * Setter.
     */
    public function setter(string $prop, mixed $value): Entity
    {
        $this->{'_'.$this->realProp($prop)} = $value;

        return $this;
    }

    /**
     * Getter.
     */
    public function getter(string $prop): mixed
    {
        return $this->{'_'.$this->realProp($prop)};
    }
}
