<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'For 循环',
    'path' => 'template/for',
    'zh-CN:description' => <<<'EOT'
如果我们需要在模板中使用 for 循环，那么通过 for 标签可以很方便地输出。
EOT,
])]
final class CompilerForTest extends TestCase
{
    use Compiler;

    #[Api([
        'zh-CN:title' => 'node 简单版',
    ])]
    public function testForNode(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% for start='1' %}
                QueryPHP - node - for <br>
            {% :for %}
            eot;

        $compiled = <<<'eot'
            <?php for ($var = 1; $var <= 0; $var += 1): ?>
                QueryPHP - node - for <br>
            <?php endfor; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    #[Api([
        'zh-CN:title' => 'node 完整版',
    ])]
    public function testForNode2(): void
    {
        $parser = $this->createParser();
        $source = <<<'eot'
            {%for start='1' end='10' var='myValue' step='3' %}
                QueryPHP for <br>
            {% :for %}
            eot;

        $compiled = <<<'eot'
            <?php for ($myValue = 1; $myValue <= 10; $myValue += 3): ?>
                QueryPHP for <br>
            <?php endfor; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testForType(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% for start='10' end='1' var='myValue' step='3' type='-' %}
                QueryPHP for <br>
            {% :for %}
            eot;

        $compiled = <<<'eot'
            <?php for ($myValue = 10; $myValue >= 1; $myValue -= 3): ?>
                QueryPHP for <br>
            <?php endfor; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
