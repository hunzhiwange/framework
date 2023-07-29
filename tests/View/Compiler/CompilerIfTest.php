<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="流程控制",
 *     path="template/if",
 *     zh-CN:description="条件表达式是最基本流程控制语句，这个在任何地方都是相当的实用。",
 * )
 *
 * @internal
 */
final class CompilerIfTest extends TestCase
{
    use Compiler;

    /**
     * @api(
     *     zh-CN:title="Node 语法流程控制",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testNodeStyle(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% if cond="(1 == $id) OR ($id > 100)" %}one
                {% elseif cond="2 == $id" %}two?
                {% else %}other?
            {% :if %}
            eot;

        $compiled = <<<'eot'
            <?php if ((1 == $id) OR ($id > 100)): ?>one
                <?php elseif (2 == $id): ?>two?
                <?php else: ?>other?
            <?php endif; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="cond 可省略",
     *     zh-CN:description="默认第一个条件会自动解析为 cond。",
     *     zh-CN:note="",
     * )
     */
    public function testNodeSimple(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% if "(1 == $id) OR ($id > 100)" %}one
                {% elseif "2 == $id" %}two?
                {% else %}other?
            {% :if %}
            eot;

        $compiled = <<<'eot'
            <?php if ((1 == $id) OR ($id > 100)): ?>one
                <?php elseif (2 == $id): ?>two?
                <?php else: ?>other?
            <?php endif; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="Node 语法流程控制支持表达式",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testNodeStyleSupportExpression(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% if cond="1 == $a->name" %}
                one
            {% :if %}

            {% if cond="1 == hello::run()" %}
                two
            {% :if %}
            eot;

        $compiled = <<<'eot'
            <?php if (1 == $a->name): ?>
                one
            <?php endif; ?>

            <?php if (1 == hello::run()): ?>
                two
            <?php endif; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
