<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class CompilerPairedTagExceptionTest extends TestCase
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
            'foreach type nodes must be used in pairs, and no corresponding tail tags are found.'.PHP_EOL.'Line:0; column:-1; file:.'
        );

        $parser = $this->createParser();

        $source = <<<'eot'
            {% foreach for=list %}
            {% :badend %}
            eot;

        $parser->doCompile($source, null, true);
    }

    public function testCross(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'if type nodes must be used in pairs, and no corresponding tail tags are found.'.PHP_EOL.'Line:1; column:4; file:.'
        );

        $parser = $this->createParser();

        $source = <<<'eot'
            {% for start='1' %}
                {% if cond="(1 == $id) OR ($id > 100)" %}one
            {% :for %}
                {% :if %}
            eot;

        $parser->doCompile($source, null, true);
    }

    public function testTagFileException(): void
    {
        $file = __DIR__.'/tag_source.html';

        $message = 'if type nodes must be used in pairs, and no corresponding tail tags are found.
Line:1; column:4; file:'.$file.'.<pre><code>{% if cond=&quot;(1 == $id) OR ($id &gt; 100)&quot; %}one
{% :for %}
<div class="template-key">    {% :</div></code></pre>';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $parser = $this->createParser();

        $source = <<<'eot'
            {% for start='1' %}
                {% if cond="(1 == $id) OR ($id > 100)" %}one
            {% :for %}
                {% :if %}
            eot;

        file_put_contents($file, $source);

        $parser->doCompile($file, null);
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
