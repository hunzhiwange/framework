<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\View\Compiler;

use Leevel\View\Compiler as Compilers;
use Tests\TestCase;

/**
 * CompilerBaseExceptionTest test.
 */
class CompilerBaseExceptionTest extends TestCase
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
                'name'     => 'not_found',
                'children' => [['is_attribute' => true]],
            ],
        ]);
    }

    public function testCheckNode3(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The node if lacks the required property: condition.'
        );

        $compilers = new Compilers();

        $this->invokeTestMethod($compilers, 'checkNode', [
            [
                'name'     => 'if',
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

        $this->assertSame([], $result);
    }

    public function testEscapeCharacter(): void
    {
        $compilers = new Compilers();

        $result = $this->invokeTestMethod($compilers, 'escapeCharacter', [
            '""',
        ]);

        $this->assertSame('', $result);
    }
}
