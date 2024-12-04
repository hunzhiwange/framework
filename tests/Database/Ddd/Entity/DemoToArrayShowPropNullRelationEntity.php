<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoToArrayShowPropNullRelationEntity extends Entity
{
    public const string TABLE = 'test';

    public const string ID = 'id';

    public const string AUTO = 'id';

    #[Struct([
    ])]
    public ?int $id = null
    {
        set (int|null $v) {
            $this->id = $this->set('id', $v);
        }
    }

    #[Struct([
    ])]
    public ?string $name = null
    {
        set (string|null $v) {
            $this->name = $this->set('name', $v);
        }
    }

    #[Struct([
    ])]
    public ?string $description = null
    {
        set (string|null $v) {
            $this->description = $this->set('description', $v);
        }
    }

    #[Struct([
        self::SHOW_PROP_NULL => '',
    ])]
    public ?string $address = null
    {
        set (string|null $v) {
            $this->address = $this->set('address', $v);
        }
    }

    #[Struct([
        self::SHOW_PROP_NULL => null,
    ])]
    public ?string $fooBar = null
    {
        set (string|null $v) {
            $this->fooBar = $this->set('fooBar', $v);
        }
    }

    #[Struct([
        self::SHOW_PROP_NULL => 'default_value',
    ])]
    public ?string $hello = null
    {
        set (string|null $v) {
            $this->hello = $this->set('hello', $v);
        }
    }

    #[Struct([
        self::HAS_ONE => DemoToArrayShowPropNullRelationTargetEntity::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
    ])]
    public ?DemoToArrayShowPropNullRelationTargetEntity $target = null
    {
        set (DemoToArrayShowPropNullRelationTargetEntity|null $v) {
            $this->target = $this->set('target', $v);
        }
    }
}
