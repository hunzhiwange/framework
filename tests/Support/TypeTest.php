<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Support\Type;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '类型',
    'path' => 'component/support/type',
    'zh-CN:description' => <<<'EOT'
QueryPHP 提供了增加 PHP 自身类型的辅助方法。
EOT,
])]
final class TypeTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '判断是否为字符串',
    ])]
    public function testTypeString(): void
    {
        static::assertTrue(Type::type('foo', 'string'));
        static::assertFalse(Type::type(1, 'string'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为整型',
    ])]
    public function testTypeInt(): void
    {
        static::assertTrue(Type::type(1, 'int'));
        static::assertTrue(Type::type(3, 'integer'));
        static::assertFalse(Type::type(true, 'int'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为浮点数',
    ])]
    public function testTypeFloat(): void
    {
        static::assertTrue(Type::type(1.1, 'float'));
        static::assertTrue(Type::type(3.14, 'double'));
        static::assertFalse(Type::type(true, 'double'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为布尔值',
    ])]
    public function testTypeBool(): void
    {
        static::assertTrue(Type::type(true, 'bool'));
        static::assertTrue(Type::type(false, 'bool'));
        static::assertFalse(Type::type(4, 'bool'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为数字',
    ])]
    public function testTypeNumeric(): void
    {
        static::assertTrue(Type::type(1.2, 'numeric'));
        static::assertTrue(Type::type(2, 'numeric'));
        static::assertTrue(Type::type('2.5', 'numeric'));
        static::assertFalse(Type::type(false, 'numeric'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为标量',
    ])]
    public function testTypeScalar(): void
    {
        static::assertTrue(Type::type(1, 'scalar'));
        static::assertTrue(Type::type('hello world', 'scalar'));
        static::assertTrue(Type::type(0, 'scalar'));
        static::assertTrue(Type::type(false, 'scalar'));
        static::assertTrue(Type::type(1.1, 'scalar'));
        static::assertTrue(Type::type(false, 'scalar'));
        static::assertFalse(Type::type([], 'scalar'));
        static::assertFalse(Type::type(null, 'scalar'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为资源',
    ])]
    public function testTypeResource(): void
    {
        $testFile = __DIR__.'/test.txt';
        file_put_contents($testFile, 'foo');
        $resource = fopen($testFile, 'r');
        static::assertTrue(Type::type($resource, 'resource'));
        static::assertFalse(Type::type(4, 'resource'));
        fclose($resource);
        unlink($testFile);
    }

    #[Api([
        'zh-CN:title' => '判断是否为闭包',
    ])]
    public function testTypeClosure(): void
    {
        static::assertTrue(Type::type(function (): void {
        }, 'Closure'));
        static::assertFalse(Type::type(true, 'Closure'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为数组',
        'zh-CN:description' => <<<'EOT'
格式支持

 * 支持 PHP 内置或者自定义的 is_array,is_int,is_custom 等函数
 * 数组支持 array:int,string 格式，值类型
 * 数组支持 array:int:string,string:array 格式，键类型:值类型
 * 数组支持 array:string:array:string:array:string:int 无限层级格式，键类型:值类型:键类型:值类型...(值类型|键类型:值类型)
EOT,
    ])]
    public function testTypeArray(): void
    {
        static::assertTrue(Type::type([], 'array'));
        static::assertTrue(Type::type([1, 2], 'array:int'));
        static::assertFalse(Type::type([1, 2], 'array:'));
        static::assertTrue(Type::type([1, 2], 'array:int:int'));
        static::assertTrue(Type::type(['foo' => 1, 'bar' => 2], 'array:string:int'));
        static::assertTrue(Type::type(['foo' => [], 'bar' => []], 'array:string:array'));
        static::assertTrue(Type::type(['foo' => [1, 2, 3], 'bar' => [4, 5, 6]], 'array:string:array:int'));
        static::assertFalse(Type::type(['foo' => [1, 2, 3], 'bar' => [4, 5, 6]], 'array:string:array:string'));
        static::assertTrue(Type::type(['foo' => ['hello' => 1], 'bar' => ['hello' => 4]], 'array:string:array:string:int'));
        static::assertTrue(Type::type(['foo' => ['hello' => ['foo' => 2]], 'bar' => ['hello' => ['foo' => 2]]], 'array:string:array:string:array:string:int'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为对象',
    ])]
    public function testTypeObject(): void
    {
        static::assertTrue(Type::type(new \stdClass(), 'object'));
        static::assertFalse(Type::type(null, 'object'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为 NULL',
    ])]
    public function testTypeNull(): void
    {
        static::assertTrue(Type::type(null, 'null'));
        static::assertFalse(Type::type(1, 'null'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为回调',
        'zh-CN:description' => <<<'EOT'
**\Tests\Support\Callback1 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\Callback1::class)]}
```
EOT,
    ])]
    public function testTypeCallback(): void
    {
        static::assertTrue(Type::type(function (): void {
        }, 'callable'));
        static::assertTrue(Type::type('md5', 'callable'));
        static::assertTrue(Type::type([new Callback1(), 'test'], 'callable'));
        static::assertTrue(Type::type([Callback1::class, 'test2'], 'callable'));
        static::assertFalse(Type::type(1, 'callable'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为对象实例',
        'zh-CN:description' => <<<'EOT'
**\Tests\Support\Callback2 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\Callback2::class)]}
```

**\Tests\Support\IInterface 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\IInterface::class)]}
```
EOT,
    ])]
    public function testTypeInstance(): void
    {
        static::assertTrue(Type::type(new \stdClass(), \stdClass::class));
        static::assertTrue(Type::type(new Callback1(), Callback1::class));
        static::assertTrue(Type::type(new Callback2(), IInterface::class));
        static::assertFalse(Type::type(1, 'callback'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为指定的几种类型',
    ])]
    public function testTypeThese(): void
    {
        static::assertTrue(Type::these('foo', ['string']));
        static::assertTrue(Type::these(1, ['string', 'int']));
    }

    public function testTypeTheseException(): void
    {
        $this->expectException(\TypeError::class);

        static::assertTrue(Type::these('foo', [[]]));
    }

    #[Api([
        'zh-CN:title' => '判断是否为数组元素类型',
        'zh-CN:description' => <<<'EOT'
格式支持

 * 数组支持 int,string 格式，值类型
 * 数组支持 int:string,string:array 格式，键类型:值类型
 * 数组支持 string:array:string:array:string:int 无限层级格式，键类型:值类型:键类型:值类型...(值类型|键类型:值类型)
EOT,
    ])]
    public function testTypeStrictArray(): void
    {
        static::assertTrue(Type::arr(['foo'], ['string']));
        static::assertFalse(Type::arr([1, 2], ['string']));
        static::assertTrue(Type::arr(['bar', 'foo'], ['string']));
        static::assertTrue(Type::arr(['bar', 2], ['string', 'int']));
        static::assertTrue(Type::arr(['hello' => 'bar', 2], ['string:string', 'int']));
        static::assertTrue(Type::arr(['hello' => 'bar', 'foo' => 'bar'], ['string:string']));
        static::assertFalse(Type::arr(['hello' => 'bar', 2], ['string:string']));
        static::assertFalse(Type::arr(['foo' => [1, 2, 3], 'bar' => [4, 5, 6]], ['string:array:string']));
        static::assertTrue(Type::arr(['foo' => ['hello' => 1], 'bar' => ['hello' => 4]], ['string:array:string:int']));
        static::assertTrue(Type::arr(['foo' => ['hello' => ['foo' => 2]], 'bar' => ['hello' => ['foo' => 2]]], ['string:array:string:array:string:int']));
    }

    public function testTypeNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Class "Leevel\\Support\\Type\\NotFound" not found');

        static::assertTrue(Type::notFound());
    }

    public function test1(): void
    {
        static::assertTrue(Type::arr([
            'base' => [__DIR__.'/assert/lang'],
        ], ['string:array']));

        static::assertTrue(Type::arr([
            'base' => [__DIR__.'/assert/lang'],
        ], ['string:array:int:string']));

        static::assertTrue(Type::arr([
            'base' => [__DIR__.'/assert/lang'],
        ], ['string:array:string']));
    }
}

class Callback1
{
    public function test(): void
    {
    }

    public static function test2(): void
    {
    }
}

interface IInterface
{
}

class Callback2 implements IInterface
{
}
