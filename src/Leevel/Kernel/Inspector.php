<?php

declare(strict_types=1);

namespace Leevel\Kernel;

use Whoops\Exception\Inspector as BaseInspector;

/**
 * Inspector.
 */
class Inspector extends BaseInspector
{
    /**
     * {@inheritDoc}
     */
    protected function getTrace($e)
    {
        return $e->getTrace();
    }
}
