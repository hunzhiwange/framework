<?php

declare(strict_types=1);

namespace Leevel\Debug\Helper;

use Leevel\Debug\Dump;

/**
 * 调试 RoadRunner 变量.
 */
function drr(mixed $var, ...$moreVars): mixed
{
    return Dump::dumpRoadRunner($var, ...$moreVars);
}

class drr
{
}
