<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class DemoToArrayEntity extends Entity
{
    use GetterSetter;

    const TABLE = 'test';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id'          => [],
        'name'        => [],
        'description' => [],
        'address'     => [],
        'foo_bar'     => [],
        'hello'       => [],
    ];
}
