<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="跳出循环",
 *     path="template/break",
 *     zh-CN:description="break 和 continue 是各种循环中非常重要的两个流程标记语言，框架当然也会支持它们。",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class CompilerBreakTest extends TestCase
{
    use Compiler;

    /**
     * @api(
     *     zh-CN:title="break 标签",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% foreach for=list %}
                {% if cond="$value > 'H'" %}
                    {% break %}
                {% :if %}
                {{ $value }}
            {% :foreach %}
            eot;

        $compiled = <<<'eot'
            <?php $index = 1; ?>
            <?php if (is_array($list)): foreach ($list as $key => $value): ?>
                <?php if ($value > 'H'): ?>
                    <?php break; ?>
                <?php endif; ?>
                <?php echo $value; ?>
            <?php $index++; ?>
            <?php endforeach; endif; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="ontinue 标签",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testContinue(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% foreach for=list %}
                {% if cond="'H' === $value" %}
                    {% continue %}
                {% :if %}
                {{ $value }}
            {% :foreach %}
            eot;

        $compiled = <<<'eot'
            <?php $index = 1; ?>
            <?php if (is_array($list)): foreach ($list as $key => $value): ?>
                <?php if ('H' === $value): ?>
                    <?php continue; ?>
                <?php endif; ?>
                <?php echo $value; ?>
            <?php $index++; ?>
            <?php endforeach; endif; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
