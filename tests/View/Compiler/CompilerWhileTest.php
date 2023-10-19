<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'While 循环',
    'path' => 'template/while',
    'zh-CN:description' => <<<'EOT'
QueryPHP 支持 while 语法标签，通过这种方式可以很好地将 PHP 的 while 语法布局出来。
EOT,
])]
final class CompilerWhileTest extends TestCase
{
    use Compiler;

    #[Api([
        'zh-CN:title' => 'node',
    ])]
    public function testNode(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {{~ $i = 10 }}
            {% while cond="$i > 0" %}
                {{ $i }}Hello QueryPHP !<br>
                {{~ $i-- }}
            {% :while %}
            eot;

        $compiled = <<<'eot'
            <?php $i = 10; ?>
            <?php while($i > 0): ?>
                <?php echo $i; ?>Hello QueryPHP !<br>
                <?php $i--; ?>
            <?php endwhile; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    #[Api([
        'zh-CN:title' => 'cond 可省略',
        'zh-CN:description' => <<<'EOT'
默认第一个条件会自动解析为 cond。
EOT,
    ])]
    public function testNodeSimple(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {{~ $i = 10 }}
            {% while "$i > 0" %}
                {{ $i }}Hello QueryPHP !<br>
                {{~ $i-- }}
            {% :while %}
            eot;

        $compiled = <<<'eot'
            <?php $i = 10; ?>
            <?php while($i > 0): ?>
                <?php echo $i; ?>Hello QueryPHP !<br>
                <?php $i--; ?>
            <?php endwhile; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
