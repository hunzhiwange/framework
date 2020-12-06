<?php

declare(strict_types=1);

namespace Tests\Kernel\Fixtures\Func;

function foo_bar(string $extend = ''): string
{
    return 'foo bar'.$extend;
}

class index
{
}
