<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\Type\Arr;
use Leevel\Support\Type\These;
use Leevel\Support\Type\Type;
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
        self::assertTrue(Type::handle('foo', 'string'));
        self::assertFalse(Type::handle(1, 'string'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为整型',
    ])]
    public function testTypeInt(): void
    {
        self::assertTrue(Type::handle(1, 'int'));
        self::assertTrue(Type::handle(3, 'integer'));
        self::assertFalse(Type::handle(true, 'int'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为浮点数',
    ])]
    public function testTypeFloat(): void
    {
        self::assertTrue(Type::handle(1.1, 'float'));
        self::assertTrue(Type::handle(3.14, 'double'));
        self::assertFalse(Type::handle(true, 'double'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为布尔值',
    ])]
    public function testTypeBool(): void
    {
        self::assertTrue(Type::handle(true, 'bool'));
        self::assertTrue(Type::handle(false, 'bool'));
        self::assertFalse(Type::handle(4, 'bool'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为数字',
    ])]
    public function testTypeNumeric(): void
    {
        self::assertTrue(Type::handle(1.2, 'numeric'));
        self::assertTrue(Type::handle(2, 'numeric'));
        self::assertTrue(Type::handle('2.5', 'numeric'));
        self::assertFalse(Type::handle(false, 'numeric'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为标量',
    ])]
    public function testTypeScalar(): void
    {
        self::assertTrue(Type::handle(1, 'scalar'));
        self::assertTrue(Type::handle('hello world', 'scalar'));
        self::assertTrue(Type::handle(0, 'scalar'));
        self::assertTrue(Type::handle(false, 'scalar'));
        self::assertTrue(Type::handle(1.1, 'scalar'));
        self::assertTrue(Type::handle(false, 'scalar'));
        self::assertFalse(Type::handle([], 'scalar'));
        self::assertFalse(Type::handle(null, 'scalar'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为资源',
    ])]
    public function testTypeResource(): void
    {
        $testFile = __DIR__.'/test.txt';
        file_put_contents($testFile, 'foo');
        $resource = fopen($testFile, 'r');
        self::assertTrue(Type::handle($resource, 'resource'));
        self::assertFalse(Type::handle(4, 'resource'));
        fclose($resource);
        unlink($testFile);
    }

    #[Api([
        'zh-CN:title' => '判断是否为闭包',
    ])]
    public function testTypeClosure(): void
    {
        self::assertTrue(Type::handle(static function (): void {}, 'Closure'));
        self::assertFalse(Type::handle(true, 'Closure'));
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
        self::assertTrue(Type::handle([], 'array'));
        self::assertTrue(Type::handle([1, 2], 'array:int'));
        self::assertFalse(Type::handle([1, 2], 'array:'));
        self::assertTrue(Type::handle([1, 2], 'array:int:int'));
        self::assertTrue(Type::handle(['foo' => 1, 'bar' => 2], 'array:string:int'));
        self::assertTrue(Type::handle(['foo' => [], 'bar' => []], 'array:string:array'));
        self::assertTrue(Type::handle(['foo' => [1, 2, 3], 'bar' => [4, 5, 6]], 'array:string:array:int'));
        self::assertFalse(Type::handle(['foo' => [1, 2, 3], 'bar' => [4, 5, 6]], 'array:string:array:string'));
        self::assertTrue(Type::handle(['foo' => ['hello' => 1], 'bar' => ['hello' => 4]], 'array:string:array:string:int'));
        self::assertTrue(Type::handle(['foo' => ['hello' => ['foo' => 2]], 'bar' => ['hello' => ['foo' => 2]]], 'array:string:array:string:array:string:int'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为对象',
    ])]
    public function testTypeObject(): void
    {
        self::assertTrue(Type::handle(new \stdClass(), 'object'));
        self::assertFalse(Type::handle(null, 'object'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为 NULL',
    ])]
    public function testTypeNull(): void
    {
        self::assertTrue(Type::handle(null, 'null'));
        self::assertFalse(Type::handle(1, 'null'));
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
        self::assertTrue(Type::handle(static function (): void {}, 'callable'));
        self::assertTrue(Type::handle('md5', 'callable'));
        self::assertTrue(Type::handle([new Callback1(), 'test'], 'callable'));
        self::assertTrue(Type::handle([Callback1::class, 'test2'], 'callable'));
        self::assertFalse(Type::handle(1, 'callable'));
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
        self::assertTrue(Type::handle(new \stdClass(), \stdClass::class));
        self::assertTrue(Type::handle(new Callback1(), Callback1::class));
        self::assertTrue(Type::handle(new Callback2(), IInterface::class));
        self::assertFalse(Type::handle(1, 'callback'));
    }

    #[Api([
        'zh-CN:title' => '判断是否为指定的几种类型',
    ])]
    public function testTypeThese(): void
    {
        self::assertTrue(These::handle('foo', ['string']));
        self::assertTrue(These::handle(1, ['string', 'int']));
    }

    public function testTypeTheseException(): void
    {
        $this->expectException(\TypeError::class);

        self::assertTrue(These::handle('foo', [[]]));
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
        self::assertTrue(Arr::handle(['foo'], ['string']));
        self::assertFalse(Arr::handle([1, 2], ['string']));
        self::assertTrue(Arr::handle(['bar', 'foo'], ['string']));
        self::assertTrue(Arr::handle(['bar', 2], ['string', 'int']));
        self::assertTrue(Arr::handle(['hello' => 'bar', 2], ['string:string', 'int']));
        self::assertTrue(Arr::handle(['hello' => 'bar', 'foo' => 'bar'], ['string:string']));
        self::assertFalse(Arr::handle(['hello' => 'bar', 2], ['string:string']));
        self::assertFalse(Arr::handle(['foo' => [1, 2, 3], 'bar' => [4, 5, 6]], ['string:array:string']));
        self::assertTrue(Arr::handle(['foo' => ['hello' => 1], 'bar' => ['hello' => 4]], ['string:array:string:int']));
        self::assertTrue(Arr::handle(['foo' => ['hello' => ['foo' => 2]], 'bar' => ['hello' => ['foo' => 2]]], ['string:array:string:array:string:int']));
    }

    public function test1(): void
    {
        self::assertTrue(Arr::handle([
            'base' => [__DIR__.'/assert/lang'],
        ], ['string:array']));

        self::assertTrue(Arr::handle([
            'base' => [__DIR__.'/assert/lang'],
        ], ['string:array:int:string']));

        self::assertTrue(Arr::handle([
            'base' => [__DIR__.'/assert/lang'],
        ], ['string:array:string']));
    }
}

class Callback1
{
    public function test(): void {}

    public static function test2(): void {}
}

interface IInterface {}

class Callback2 implements IInterface {}
