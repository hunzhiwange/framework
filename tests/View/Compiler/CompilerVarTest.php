<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="变量",
 *     path="template/var",
 *     zh-CN:description="变量是最基本的用法，这里模板引擎做了大量的工作支持更好。",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class CompilerVarTest extends TestCase
{
    use Compiler;

    /**
     * @api(
     *     zh-CN:title="最简单一个普通变量",
     *     zh-CN:description="",
     *     zh-CN:note="模板标签的 “{{” 和 “$” 之间可以有空格，建议保持一个空格，保持整洁。",
     * )
     */
    public function testBaseUse(): void
    {
        $parser = $this->createParser();

        // 普通变量
        $source = <<<'eot'
            {{ $name }}
            eot;

        $compiled = <<<'eot'
            <?php echo $name; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="输出一个数组",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testArraySupport(): void
    {
        $parser = $this->createParser();

        // 数组支持
        $source = <<<'eot'
            我的梦想是写好”{{ $value['name'] }}“，我相信”{{ $value['description'] }}“。
            eot;

        $compiled = <<<'eot'
            我的梦想是写好”<?php echo $value['name']; ?>“，我相信”<?php echo $value['description']; ?>“。
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="输出一个对象",
     *     zh-CN:description="我们编写这样子一个简单对象，然后再赋值。",
     *     zh-CN:note="",
     * )
     */
    public function testObject(): void
    {
        $parser = $this->createParser();

        // 输出一个对象
        $source = <<<'eot'
            我的梦想是写好”{{ $demo->name }}“，我相信”{{ $demo->description }}“。
            eot;

        $compiled = <<<'eot'
            我的梦想是写好”<?php echo $demo->name; ?>“，我相信”<?php echo $demo->description; ?>“。
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="无限级支持",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testLevel(): void
    {
        $parser = $this->createParser();

        // 对象无限层级支持
        $source = <<<'eot'
            我的梦想是写好”{{ $demo->name->child->child->child }}“，我相信”{{ $demo->description }}“。
            eot;

        $compiled = <<<'eot'
            我的梦想是写好”<?php echo $demo->name->child->child->child; ?>“，我相信”<?php echo $demo->description; ?>“。
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="运算符.加减法运算",
     *     zh-CN:description="我们有的时候需要进行一些字符串的操作，以及变量之间的运算，当然直接使用 PHP 可以进行这样子的操作。这里，我们给出的是另一种简单的语法规则。",
     *     zh-CN:note="",
     * )
     */
    public function testOperator(): void
    {
        $parser = $this->createParser();

        // 变量之间的加减法运算
        $source = <<<'eot'
            {{ $value+$value2 }}
            {{ $value-$value2 }}
            eot;

        $compiled = <<<'eot'
            <?php echo $value+$value2; ?>
            <?php echo $value-$value2; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="运算符.乘除余数",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testOperator2(): void
    {
        $parser = $this->createParser();

        // 变量之间的乘除余数
        $source = <<<'eot'
            {{ $value + 9 +10 }}
            {{ $value * $value2 * 10 }}
            {{ $value / $value2 }}
            {{ $value3+$list['key'] }}
            {{ $value3%$list['key'] }}
            eot;

        $compiled = <<<'eot'
            <?php echo $value + 9 +10; ?>
            <?php echo $value * $value2 * 10; ?>
            <?php echo $value / $value2; ?>
            <?php echo $value3+$list['key']; ?>
            <?php echo $value3%$list['key']; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="运算符.连接字符",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testOperator3(): void
    {
        $parser = $this->createParser();

        // 变量之间的连接字符
        $source = <<<'eot'
            {{ $value3.'start - '.$value.$value2.'- end' }}
            eot;

        $compiled = <<<'eot'
            <?php echo $value3.'start - '.$value.$value2.'- end'; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="函数支持",
     *     zh-CN:description="
     * 仅仅是输出变量并不能满足模板输出的需要，内置模板引擎支持对模板变量使用调节器和格式化功能，其实也就是提供函数支持，并支持多个函数同时使用。
     *
     * 用于模板标签的函数可以是PHP 内置函数或者是用户自定义函数，和 smarty 不同的是用于模板的函数不需要特别的定义。
     *
     * ### 函数调用格式
     *
     * ``` php
     * {{ $varName|function1|function2=arg1,arg2,** }}
     * ```
     *
     * 说明：
     *
     * * 表示模板变量本身的参数位置
     * * 支持多个函数，函数之间支持空格
     * ",
     *     zh-CN:note="函数的定义和使用顺序的对应关系，通常来说函数的第一个参数就是前面的变量或者前一个函数使用的结果，如果你的变量并不是函数的第一个参数，需要使用定位符号 “**”。",
     * )
     */
    public function testFunction(): void
    {
        $parser = $this->createParser();

        // base
        $source = <<<'eot'
            {{ $varName|function1|function2=arg1,arg2,** }}
            eot;

        $compiled = <<<'eot'
            <?php echo function2(arg1,arg2,function1($varName)); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));

        // 模板中如果不加 ** 的格式为
        $source = <<<'eot'
            {{ $varName|function1|function2=arg1,arg2 }}
            eot;

        $compiled = <<<'eot'
            <?php echo function2(function1($varName), arg1,arg2); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="函数支持.基本用法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testFunction2(): void
    {
        $parser = $this->createParser();

        // 例 1
        $source = <<<'eot'
            {{ $content|strtoupper|substr=0,3 }}
            eot;

        $compiled = <<<'eot'
            <?php echo substr(strtoupper($content), 0,3); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="函数支持.占位符",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testFunction3(): void
    {
        $parser = $this->createParser();

        // 例 2
        $source = <<<'eot'
            {{ $date|date="Y-m-d",** }}
            eot;

        $compiled = <<<'eot'
            <?php echo date("Y-m-d",$date); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="函数支持.快捷方法",
     *     zh-CN:description="并且还提供了在模板文件中直接调用函数的快捷方法，无需通过模板变量，包括两种方式：",
     *     zh-CN:note="",
     * )
     */
    public function testFunction4(): void
    {
        $parser = $this->createParser();

        // 例 3
        $source = <<<'eot'
            {{: function1($var) }}
            eot;

        $compiled = <<<'eot'
            <?php echo function1($var); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="函数支持.静态方法",
     *     zh-CN:description="使用静态函数来格式化参数。",
     *     zh-CN:note="",
     * )
     */
    public function testFunction5(): void
    {
        $parser = $this->createParser();

        // 静态方法
        $source = <<<'eot'
            {{~ $currentTime=time() }}
            {{ $currentTime|\Leevel\Support\Str::formatDate }}
            eot;

        $compiled = <<<'eot'
            <?php $currentTime=time(); ?>
            <?php echo \Leevel\Support\Str::formatDate($currentTime); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="函数支持.执行方法但不输出",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testFunction6(): void
    {
        $parser = $this->createParser();

        // 执行方法但不输出
        $source = <<<'eot'
            {{~ function1($var) }}
            eot;

        $compiled = <<<'eot'
            <?php function1($var); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));

        // 例 1
        $source = <<<'eot'
            {{~ echo('Hello world!') }}
            eot;

        $compiled = <<<'eot'
            <?php echo('Hello world!'); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="函数支持.对象方法",
     *     zh-CN:description="如果我们需要在模板中使用对象的方法，那么通过代码版本的变量语法可以很方便地输出。",
     *     zh-CN:note="程序编译后默认是输出值，所以最好在类的方法中最好不要直接输出值，直接返回，这样可以交给模版来做数据处理。",
     * )
     */
    public function testFunction7(): void
    {
        $parser = $this->createParser();

        // 对象方法
        $source = <<<'eot'
            {{ $demo->test() }}
            eot;

        $compiled = <<<'eot'
            <?php echo $demo->test(); ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="函数支持.默认值",
     *     zh-CN:description="
     * 如果输出的模板变量没有值，但是我们需要在显示的时候赋予一个默认值的话，可以使用 default 语法，格式：
     *
     * ``` php
     * {{ $变量|default="默认值" }}
     * ```
     *
     * > 这里的 default 不是函数，而是系统的一个语法规则。
     * ",
     *     zh-CN:note="“default=” 之间不能有空格，否则无法识别。",
     * )
     */
    public function testFunction8(): void
    {
        $parser = $this->createParser();

        // 三元运算符
        $source = <<<'eot'
            {{~ $name='' }}
            {{ $name|default="Hello，我最爱的雪碧！" }}

            {{~ $name='肯德基更配！' }}
            {{ $name|default="Hello，我最爱的雪碧！" }}
            eot;

        $compiled = <<<'eot'
            <?php $name=''; ?>
            <?php echo $name ?: "Hello，我最爱的雪碧！"; ?>

            <?php $name='肯德基更配！'; ?>
            <?php echo $name ?: "Hello，我最爱的雪碧！"; ?>
            eot;

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
