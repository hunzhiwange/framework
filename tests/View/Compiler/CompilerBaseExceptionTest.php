<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Leevel\View\Compiler as Compilers;
use Tests\TestCase;

final class CompilerBaseExceptionTest extends TestCase
{
    public function testCheckNode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Tag attribute type validation failed.'
        );

        $compilers = new Compilers();

        $this->invokeTestMethod($compilers, 'checkNode', [
            [
                'children' => [['is_attribute' => false]],
            ],
        ]);
    }

    public function testCheckNode2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The tag not_found is undefined.'
        );

        $compilers = new Compilers();

        $this->invokeTestMethod($compilers, 'checkNode', [
            [
                'name' => 'not_found',
                'children' => [['is_attribute' => true]],
            ],
        ]);
    }

    public function testCheckNode3(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The node if lacks the required property: cond.'
        );

        $compilers = new Compilers();

        $this->invokeTestMethod($compilers, 'checkNode', [
            [
                'name' => 'if',
                'children' => [['is_attribute' => true]],
            ],
        ]);
    }

    public function testGetNodeAttribute(): void
    {
        $compilers = new Compilers();

        $result = $this->invokeTestMethod($compilers, 'getNodeAttribute', [
            [
                'children' => [],
            ],
        ]);

        static::assertSame([], $result);
    }

    public function testEscapeCharacter(): void
    {
        $compilers = new Compilers();

        $result = $this->invokeTestMethod($compilers, 'escapeCharacter', [
            '""',
        ]);

        static::assertSame('', $result);
    }
}
