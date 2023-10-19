<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils;

use Leevel\Kernel\Utils\ClassParser;
use Tests\TestCase;

final class ClassParserTest extends TestCase
{
    public function testBaseUse(): void
    {
        $classParser = new ClassParser();
        $className = $classParser->handle(__DIR__.'/ClassParserTest.php');
        static::assertSame($className, self::class);
    }
}
