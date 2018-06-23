<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\View\Compiler;

use Tests\TestCase;

/**
 * compiler var test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.06
 *
 * @version 1.0
 */
class CompilerVarTest extends TestCase
{
    use Compiler;

    public function testBaseUse()
    {
        $parser = $this->createParser();

        // 普通变量
        $source = <<<'eot'
{$name}
eot;

        $compiled = <<<'eot'
<?php echo $name;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // JS 风格变量
        $source = <<<'eot'
{{ value }}
eot;

        $compiled = <<<'eot'
<?php echo $value;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 数组支持
        $source = <<<'eot'
我的梦想是写好”{$value['name']}“，我相信”{$value['description']}“。
eot;

        $compiled = <<<'eot'
我的梦想是写好”<?php echo $value['name'];?>“，我相信”<?php echo $value['description'];?>“。
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // JS 风格数组支持
        $source = <<<'eot'
{{ value['test'] }}
eot;

        $compiled = <<<'eot'
<?php echo $value['test'];?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 输出一个对象
        $source = <<<'eot'
我的梦想是写好”{$demo->name}“，我相信”{$demo->description}“。
eot;

        $compiled = <<<'eot'
我的梦想是写好”<?php echo $demo->name;?>“，我相信”<?php echo $demo->description;?>“。
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // JS 风格输出一个对象
        // . 周围有空格表示变量
        $source = <<<'eot'
<li><a href="{{ item.href }}">{{ item.caption }}</a></li>
eot;

        $source = <<<'eot'
{{ a.b }}
{{ a . b }}
{{ a->b }}
eot;

        $compiled = <<<'eot'
<?php echo $a->b;?>
<?php echo $a . $b;?>
<?php echo $a->b;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 对象无限层级支持
        $source = <<<'eot'
我的梦想是写好”{$demo->name->child->child->child}“，我相信”{$demo->description}“。
eot;

        $compiled = <<<'eot'
我的梦想是写好”<?php echo $demo->name->child->child->child;?>“，我相信”<?php echo $demo->description;?>“。
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 对象数组点语法支持
        $source = <<<'eot'
我的梦想是写好”{$demo.name}“，我相信”{$demo.description}“。
eot;

        $compiled = <<<'eot'
我的梦想是写好”<?php echo $demo->name;?>“，我相信”<?php echo $demo->description;?>“。
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 支持无限级对象属性
        $source = <<<'eot'
我的梦想是写好”{$demo.name.one.two.three.four}“，我相信”{$demo.description.one.two.three.four}“。
eot;

        $compiled = <<<'eot'
我的梦想是写好”<?php echo $demo->name->one->two->three->four;?>“，我相信”<?php echo $demo->description->one->two->three->four;?>“。
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testOperator()
    {
        $parser = $this->createParser();

        // 变量之间的加减法运算
        $source = <<<'eot'
{$value+$value2}
{$value-$value2}
eot;

        $compiled = <<<'eot'
<?php echo $value+$value2;?>
<?php echo $value-$value2;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 变量之间的乘除余数
        $source = <<<'eot'
{$value + 9 +10}
{$value * $value2 * 10}
{$value / $value2}
{$value3+$list['key']}
{$value3%$list['key']}
eot;

        $compiled = <<<'eot'
<?php echo $value + 9 +10;?>
<?php echo $value * $value2 * 10;?>
<?php echo $value / $value2;?>
<?php echo $value3+$list['key'];?>
<?php echo $value3%$list['key'];?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 变量之间的连接字符
        $source = <<<'eot'
{$value3.'start - '.$value.$value2.'- end'}
eot;

        $compiled = <<<'eot'
<?php echo $value3.'start - '.$value.$value2.'- end';?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testJsOperator()
    {
        $parser = $this->createParser();

        // 变量之间的加减法运算
        $source = <<<'eot'
{{ value+value2 }}
{{ value-value2 }}
eot;

        $compiled = <<<'eot'
<?php echo $value+$value2;?>
<?php echo $value-$value2;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 变量之间的乘除余数
        $source = <<<'eot'
{{ value + 9 +10 }}
{{ value * value2 * 10 }}
{{ value / value2 }}
{{ value3+list['key'] }}
{{ value3%list['key'] }}
eot;

        $compiled = <<<'eot'
<?php echo $value + 9 +10;?>
<?php echo $value * $value2 * 10;?>
<?php echo $value / $value2;?>
<?php echo $value3+$list['key'];?>
<?php echo $value3%$list['key'];?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 变量之间的连接字符
        $source = <<<'eot'
{{ value3.'start - '. value. value2.'end' }}
eot;

        $compiled = <<<'eot'
<?php echo $value3.'start - '. $value. $value2.'end';?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testFunction()
    {
        $parser = $this->createParser();

        // base
        $source = <<<'eot'
{$varName|function1|function2=arg1,arg2,**}
eot;

        $compiled = <<<'eot'
<?php echo function2(arg1,arg2,function1($varName));?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 模板中如果不加 ** 的格式为
        $source = <<<'eot'
{$varName|function1|function2=arg1,arg2}
eot;

        $compiled = <<<'eot'
<?php echo function2(function1($varName), arg1,arg2);?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 例 1
        $source = <<<'eot'
{$content|strtoupper|substr=0,3}
eot;

        $compiled = <<<'eot'
<?php echo substr(strtoupper($content), 0,3);?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 例 2
        $source = <<<'eot'
{$date|date="Y-m-d",**}
eot;

        $compiled = <<<'eot'
<?php echo date("Y-m-d",$date);?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 例 3
        $source = <<<'eot'
{:function1($var)}
eot;

        $compiled = <<<'eot'
<?php echo function1($var);?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 静态方法
        $source = <<<'eot'
{~$currentTime=time()}
{$currentTime|\Leevel\Support\Str::smartDate}
eot;

        $compiled = <<<'eot'
<?php $currentTime=time();?>
<?php echo \Leevel\Support\Str::smartDate($currentTime);?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 执行方法但不输出
        $source = <<<'eot'
{~function1($var)}
eot;

        $compiled = <<<'eot'
<?php function1($var);?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 例 1
        $source = <<<'eot'
{~echo('Hello world!')}
eot;

        $compiled = <<<'eot'
<?php echo('Hello world!');?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 对象方法
        $source = <<<'eot'
{$demo->test()}
eot;

        $compiled = <<<'eot'
<?php echo $demo->test();?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 三元运算符
        $source = <<<'eot'
{~$name=''}
{$name|default="Hello，我最爱的雪碧！"}

{~$name='肯德基更配！'}
{$name|default="Hello，我最爱的雪碧！"}
eot;

        $compiled = <<<'eot'
<?php $name='';?>
<?php echo $name ?: "Hello，我最爱的雪碧！";?>

<?php $name='肯德基更配！';?>
<?php echo $name ?: "Hello，我最爱的雪碧！";?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testJsFunction()
    {
        $parser = $this->createParser();

        // 例 1
        $source = <<<'eot'
{{ var|escape }}
{{ var|e }}
eot;

        $compiled = <<<'eot'
<?php echo escape($var);?>
<?php echo e($var);?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 例 2
        $source = <<<'eot'
{{ list|join=',' }}
eot;

        $compiled = <<<'eot'
<?php echo join($list, ',');?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 例 3
        $source = <<<'eot'
{{ data|convert_encoding='iso-2022-jp', 'UTF-8') }}
eot;

        $compiled = <<<'eot'
<?php echo convert_encoding($data, 'iso-2022-jp', 'UTF-8'));?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 例 4
        $source = <<<'eot'
{{ data|convert_encoding='iso-2022-jp', **, 'UTF-8') }}
eot;

        $compiled = <<<'eot'
<?php echo convert_encoding('iso-2022-jp', $data, 'UTF-8'));?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
