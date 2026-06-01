<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoCreateAutoFillEntity extends Entity
{
    public const string TABLE = 'test';

    public const string ID = 'id';

    public const string AUTO = 'id';

    #[Struct([
        self::READONLY => true,
    ])]
    public ?int $id = null {
        set(int|string|null $v) {
            $this->id = $this->set('id', $v);
        }
    }

    #[Struct([
        self::CREATE_FILL => 'name for '.self::CREATE_FILL,
    ])]
    public ?string $name = null {
        set(string|null $v) {
            $this->name = $this->set('name', $v);
        }
    }

    #[Struct([
        self::CREATE_FILL => null,
    ])]
    public ?string $description = null {
        set(string|null $v) {
            $this->description = $this->set('description', $v);
        }
    }

    #[Struct([
        self::CREATE_FILL => null,
    ])]
    public ?string $address = null {
        set(string|null $v) {
            $this->address = $this->set('address', $v);
        }
    }

    #[Struct([
        self::CREATE_FILL => null,
    ])]
    public ?string $fooBar = null {
        set(string|null $v) {
            $this->fooBar = $this->set('fooBar', $v);
        }
    }

    #[Struct([
        self::CREATE_FILL => null,
    ])]
    public ?string $hello = null {
        set(string|null $v) {
            $this->hello = $this->set('hello', $v);
        }
    }

    protected function fillDescription(mixed $old): string
    {
        return 'set description.';
    }

    protected function fillAddress(mixed $old): string
    {
        return 'address is set now.';
    }

    protected function fillFooBar(mixed $old): string
    {
        return 'foo bar.';
    }

    protected function fillHello(mixed $old): string
    {
        return 'hello field.';
    }
}
