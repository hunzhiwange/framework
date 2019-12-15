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

use Tests\TestCase;

class CompilerPairedTagExceptionTest extends TestCase
{
    use Compiler;

    protected function tearDown(): void
    {
        $file = __DIR__.'/tag_source.html';

        if (is_file($file)) {
            unlink($file);
        }
    }

    public function testBaseUse(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'list type nodes must be used in pairs, and no corresponding tail tags are found.<br />Line:0; column:-1; file:.'
        );

        $parser = $this->createParser();

        $source = <<<'eot'
            <list for=list>
            </badend>
            eot;

        $parser->doCompile($source, null, true);
    }

    public function testCross(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'if type nodes must be used in pairs, and no corresponding tail tags are found.<br />Line:1; column:4; file:.'
        );

        $parser = $this->createParser();

        $source = <<<'eot'
            <for start='1'>
                <if condition="($id eq 1) OR ($id gt 100)">one
            </for>
                </if>
            eot;

        $parser->doCompile($source, null, true);
    }

    public function testTagFileException(): void
    {
        $file = __DIR__.'/tag_source.html';

        $source = 'Line:1; column:4; file:'.$file.'.<pre><code>&lt;if condition=&quot;($id eq 1) OR ($id gt 100)&quot;&gt;one
&lt;/for&gt;
<div class="template-key">    &lt;/if</div></code></pre>';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($source);

        $parser = $this->createParser();

        $source = <<<'eot'
            <for start='1'>
                <if condition="($id eq 1) OR ($id gt 100)">one
            </for>
                </if>
            eot;

        file_put_contents($file, $source);

        $parser->doCompile($file, null);
    }

    public function testSimpleWithoutException(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {for $i=1;$i<10;$i++}
                {if $foo}
            {/for}
                {/if}
            eot;

        $compiled = <<<'eot'
            <?php for ($i=1;$i<10;$i++): ?>
                <?php if ($foo): ?>
            <?php endfor; ?>
                <?php endif; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testTagCrossException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Template engine tag library does not support cross.'
        );

        $parser = $this->createParser();

        $this->invokeTestMethod($parser, 'positionRelative', [
            ['start' => 8, 'end' => 16],
            ['start' => 5, 'end' => 12],
        ]);
    }
}
