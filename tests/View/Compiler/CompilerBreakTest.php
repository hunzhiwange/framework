<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Leevel\Kernel\Utils\Api;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '跳出循环',
    'path' => 'template/break',
    'zh-CN:description' => <<<'EOT'
break 和 continue 是各种循环中非常重要的两个流程标记语言，框架当然也会支持它们。
EOT,
])]
final class CompilerBreakTest extends TestCase
{
    use Compiler;

    #[Api([
        'zh-CN:title' => 'break 标签',
    ])]
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

    #[Api([
        'zh-CN:title' => 'ontinue 标签',
    ])]
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
