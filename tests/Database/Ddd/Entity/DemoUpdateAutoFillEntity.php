<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoUpdateAutoFillEntity extends Entity
{
    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $id = null;

    #[Struct([
        self::UPDATE_FILL => 'name for '.self::UPDATE_FILL,
    ])]
    protected ?string $name = null;

    #[Struct([
        self::UPDATE_FILL => null,
    ])]
    protected ?string $description = null;

    #[Struct([
        self::UPDATE_FILL => null,
    ])]
    protected ?string $address = null;

    #[Struct([
        self::UPDATE_FILL => null,
    ])]
    protected ?string $fooBar = null;

    #[Struct([
        self::UPDATE_FILL => null,
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
