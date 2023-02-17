<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoCreateAutoFillEntity extends Entity
{
    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $id = null;

    #[Struct([
        self::CREATE_FILL => 'name for '.self::CREATE_FILL,
    ])]
    protected ?string $name = null;

    #[Struct([
        self::CREATE_FILL => null,
    ])]
    protected ?string $description = null;

    #[Struct([
        self::CREATE_FILL => null,
    ])]
    protected ?string $address = null;

    #[Struct([
        self::CREATE_FILL => null,
    ])]
    protected ?string $fooBar = null;

    #[Struct([
        self::CREATE_FILL => null,
    ])]
    protected ?string $hello = null;

    protected function fillDescription($old): string
    {
        return 'set description.';
    }

    protected function fillAddress($old): string
    {
        return 'address is set now.';
    }

    protected function fillFooBar($old): string
    {
        return 'foo bar.';
    }

    protected function fillHello($old): string
    {
        return 'hello field.';
    }
}
