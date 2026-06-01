<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;
use Leevel\Validate\IValidator;

class EntityWithEnumValidator extends Entity
{
    public const string TABLE = 'entity_with_enum';

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
    public ?string $title = null {
        set(string|null $v) {
            $this->title = $this->set('title', $v);
        }
    }

    #[Struct([
        self::ENUM_CLASS => StatusEnum::class,
    ])]
    public ?int $status = null {
        set(int|string|null $v) {
            $this->status = $this->set('status', $v);
        }
    }
}
