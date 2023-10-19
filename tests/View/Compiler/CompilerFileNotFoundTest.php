<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Tests\TestCase;

final class CompilerFileNotFoundTest extends TestCase
{
    use Compiler;

    public function testBaseUse(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('File %s is not exits.', __DIR__.'/not_found.html')
        );

        $parser = $this->createParser();

        $parser->doCompile(__DIR__.'/not_found.html');
    }
}
