<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;
use Leevel\Validate\IValidator;

class EntityWithEnumValidator extends Entity
{
    public const TABLE = 'entity_with_enum';

    public const ID = 'id';

    public const AUTO = 'id';

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $id = null;

    #[Struct([
        self::COLUMN_VALIDATOR => [
            self::VALIDATOR_SCENES => [
                'required',
                'max_length:30',
            ],
            'store' => null,
            ':update' => [
                IValidator::OPTIONAL,
            ],
            'update_new' => [
                'required',
                'max_length:10',
            ],
        ],
    ])]
    protected ?string $title = null;

    #[Struct([
        self::ENUM_CLASS => StatusEnum::class,
    ])]
    protected ?int $status = null;
}
