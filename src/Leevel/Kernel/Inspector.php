<?php

declare(strict_types=1);

namespace Leevel\Kernel;

use Whoops\Exception\Inspector as BaseInspector;

/**
 * Inspector.
 */
class Inspector extends BaseInspector
{
    protected function getTrace($e)
    {
        return $e->getTrace();
    }
}
