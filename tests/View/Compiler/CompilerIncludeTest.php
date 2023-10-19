<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'Include 标签',
    'path' => 'template/include',
    'zh-CN:description' => <<<'EOT'
可以使用 include 标签来包含外部的模板文件。
EOT,
])]
final class CompilerIncludeTest extends TestCase
{
    use Compiler;

    #[Api([
        'zh-CN:title' => '使用完整文件名包含',
        'zh-CN:description' => <<<'EOT'
使用方法如下：

``` html
{% include file="完整模板文件名" %}
```

这种情况下，模板文件名必须包含后缀。
EOT,
    ])]
    public function testBaseUse(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% include file="assets/themes/header.html" %}
            eot;

        $compiled = <<<'eot'
            <?php echo $this->display('assets/themes/header', [], '.html'); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    #[Api([
        'zh-CN:title' => '使用 ext 定义模板文件后缀',
    ])]
    public function testExt(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% include file="hello" ext=".tpl" %}
            eot;

        $compiled = <<<'eot'
            <?php echo $this->display('hello', [], '.tpl'); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    #[Api([
        'zh-CN:title' => '使用变量定义完整的文件',
    ])]
    public function testVar(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {{~ $headTpl = \Leevel::themesPath() . '/' . 'header.html' }}
            {% include file="$headTpl" %}
            eot;

        $compiled = <<<'eot'
            <?php $headTpl = \Leevel::themesPath() . '/' . 'header.html'; ?>
            <?php echo $this->display($headTpl); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    #[Api([
        'zh-CN:title' => '包含当前视图目录下的模板文件',
    ])]
    public function testInViewDir(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% include file="test" %}
            eot;

        $compiled = <<<'eot'
            <?php echo $this->display('test'); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    #[Api([
        'zh-CN:title' => '包含其他模块的操作模板',
        'zh-CN:description' => <<<'EOT'
其中模块以目录分隔
EOT,
    ])]
    public function testOtherModule(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% include file="public/header" %}
            eot;

        $compiled = <<<'eot'
            <?php echo $this->display('public/header'); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    #[Api([
        'zh-CN:title' => '函数表达式支持',
        'zh-CN:description' => <<<'EOT'
表达式语法为 `()` 包裹起来并且括号周围不能有空格。
EOT,
    ])]
    public function testExpr(): void
    {
        $parser = $this->createParser();

        // 防止 . 被替换加上 () 包裹起来
        $source = <<<'eot'
            {% include file="($path . '/' . $name)" %}
            {% include file="(Template::tpl('header'))" %}
            {% include file="(tpl('header'))" %}
            {% include file=" (not_expression) " %}
            {% include file="1 (not_expression) " %}
            {% include file="$hello" %}
            eot;

        $compiled = <<<'eot'
            <?php echo $this->display($path . '/' . $name); ?>
            <?php echo $this->display(Template::tpl('header')); ?>
            <?php echo $this->display(tpl('header')); ?>
            <?php echo $this->display(' (not_expression) '); ?>
            <?php echo $this->display('1 (not_expression) '); ?>
            <?php echo $this->display($hello); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function test1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The node include lacks the required property: file.'
        );

        $parser = $this->createParser();
        $source = <<<'eot'
            {% include %}
            eot;

        $parser->doCompile($source, null, true);
    }

    public function test2(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% include file="" %}
            eot;

        $compiled = <<<'eot'
            <?php echo $this->display(''); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
