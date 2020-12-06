<?php

declare(strict_types=1);

namespace Tests\Kernel\Fixtures\Func;

use Error;

function helper_fn_throw()
{
    throw new Error('not');
}

class helper_fn_throw
{
}
