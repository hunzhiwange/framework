<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Leevel\View\Compiler as Compilers;
use Leevel\View\Parser;

/**
 * compiler trait.
 */
trait Compiler
{
    protected function createParser(): Parser
    {
        return (new Parser(new Compilers()))
            ->registerCompilers()
            ->registerParsers()
        ;
    }
}
