<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Tests\TestCase;

#[Api([
    'zh-CN:title' => '快捷标签',
    'path' => 'template/quick',
    'zh-CN:description' => <<<'EOT'
为了使得模板定义更加简洁，系统还支持一些常用的变量输出快捷标签。
EOT,
])]
final class CompilerQuickTest extends TestCase
{
    use Compiler;

    #[Api([
        'zh-CN:title' => '# 注释标签',
        'zh-CN:description' => <<<'EOT'
模板中的注释仅供模板制作人员查看，最终不会显示出来。
EOT,
    ])]
    public function testBaseUse(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {{# 我是一个注释 #}}

            {{#
                我是两行注释
              Thank U!
            #}}
            eot;

        $compiled = <<<'eot'



            eot;

        static::assertSame(trim($compiled), trim($parser->doCompile($source, null, true)));
    }

    #[Api([
        'zh-CN:title' => '~ 原样 PHP 标签',
    ])]
    public function testOriginalPhp(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {{~ $value = 'Make QueryPHP greater !' }}
            {{ $value }}
            eot;

        $compiled = <<<'eot'
            <?php $value = 'Make QueryPHP greater !'; ?>
            <?php echo $value; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    #[Api([
        'zh-CN:title' => ': echo 快捷方式',
    ])]
    public function testEcho(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {{: 'Hello QueryPHP!' }}
            eot;

        $compiled = <<<'eot'
            <?php echo 'Hello QueryPHP!'; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
