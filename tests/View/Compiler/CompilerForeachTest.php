<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Leevel\Kernel\Utils\Api;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'Foreach 循环',
    'path' => 'template/foreach',
    'zh-CN:description' => <<<'EOT'
foreach 标签也是用于循环输出，解析后的本质为 foreach。
EOT,
])]
final class CompilerForeachTest extends TestCase
{
    use Compiler;

    #[Api([
        'zh-CN:title' => 'node',
    ])]
    public function testNode(): void
    {
        $parser = $this->createParser();
        $source = <<<'eot'
            {% foreach for=list value=my_value key=my_key index=my_index %}
                {{ $my_index }} {{ $my_key }} {{ $my_value }}
            {% :foreach %}
            eot;

        $compiled = <<<'eot'
            <?php $my_index = 1; ?>
            <?php if (is_array($list)): foreach ($list as $my_key => $my_value): ?>
                <?php echo $my_index; ?> <?php echo $my_key; ?> <?php echo $my_value; ?>
            <?php $my_index++; ?>
            <?php endforeach; endif; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    #[Api([
        'zh-CN:title' => 'node 省略键值',
        'zh-CN:description' => <<<'EOT'
有时候我们不需要键值，这个时候我们在模板中写下如下的代码：
EOT,
    ])]
    public function testNodeSimple(): void
    {
        $parser = $this->createParser();
        $source = <<<'eot'
            {% foreach for=list %}
                {{ $index }} {{ $key }} {{ $value }}
            {% :foreach %}
            eot;

        $compiled = <<<'eot'
            <?php $index = 1; ?>
            <?php if (is_array($list)): foreach ($list as $key => $value): ?>
                <?php echo $index; ?> <?php echo $key; ?> <?php echo $value; ?>
            <?php $index++; ?>
            <?php endforeach; endif; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
