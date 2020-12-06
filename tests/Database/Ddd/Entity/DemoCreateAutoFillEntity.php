<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class DemoCreateAutoFillEntity extends Entity
{
    use GetterSetter;

    const TABLE = 'test';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            self::READONLY => true,
        ],
        'name' => [
            self::CREATE_FILL       => 'name for '.self::CREATE_FILL,
        ],
        'description' => [
            self::CREATE_FILL   => null,
        ],
        'address' => [
            self::CREATE_FILL    => null,
        ],
        'foo_bar' => [
            self::CREATE_FILL    => null,
        ],
        'hello' => [
            self::CREATE_FILL      => null,
        ],
    ];

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
