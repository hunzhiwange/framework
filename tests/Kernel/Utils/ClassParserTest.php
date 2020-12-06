<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils;

use Leevel\Kernel\Utils\ClassParser;
use Tests\TestCase;

class ClassParserTest extends TestCase
{
    public function testBaseUse(): void
    {
        $classParser = new ClassParser();
        $className = $classParser->handle(__DIR__.'/ClassParserTest.php');
        $this->assertSame($className, self::class);
    }
}
