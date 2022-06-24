<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否可接受的.
 */
function accepted(mixed $value): bool
{
    return required($value) &&
        in_array($value, [
            'yes',
            'on',
            't',
            '1',
            1,
            true,
            'true',
        ], true);
}

class accepted
{
}

// import fn.
class_exists(required::class);
