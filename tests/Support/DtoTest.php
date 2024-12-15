<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Tests\Support\Fixtures\Dto1;
use Tests\Support\Fixtures\Dto2;
use Tests\Support\Fixtures\Dto3;
use Tests\Support\Fixtures\Dto4;
use Tests\Support\Fixtures\Dto5;
use Tests\Support\Fixtures\Dto6;
use Tests\Support\Fixtures\Dto7;
use Tests\Support\Fixtures\DtoProp1;
use Tests\Support\Fixtures\DtoProp2;
use Tests\Support\Fixtures\DtoToArray;
use Tests\Support\Fixtures\DtoToArray2;
use Tests\Support\Fixtures\DtoToArray3;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '数据传输对象',
    'path' => 'component/support/dto',
    'zh-CN:description' => <<<'EOT'
QueryPHP 提供了一个简单的数据传输对象组件。
EOT,
])]
final class DtoTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'all 获取全部属性数据（下划线属性命名风格）',
    ])]
    public function testAllUnCamelizeStyle(): void
    {
        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => $dtoProp1 = new DtoProp1(),
            'demoObject2Prop' => $dtoProp2 = new DtoProp2(),
            'demoObject3Prop' => $dto2 = new Dto2(['demoStringProp' => 'hello world']),
        ]);

        $data = $dto1->all();
        self::assertSame('foo', $data['demo_string_prop']);
        self::assertSame(1, $data['demo_int_prop']);
        self::assertSame(1.5, $data['demo_float_prop']);
        self::assertTrue($data['demo_true_prop']);
        self::assertFalse($data['demo_false_prop']);
        self::assertSame($dtoProp1, $data['demo_object_prop']);
        self::assertSame($dtoProp2, $data['demo_object2_prop']);
        self::assertSame($dto2, $data['demo_object3_prop']);
        self::assertTrue($data['demo_mixed_prop']);
    }

    #[Api([
        'zh-CN:title' => 'all 获取全部属性数据（驼峰属性命名风格）',
    ])]
    public function testAllCamelizeStyle(): void
    {
        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => $dtoProp1 = new DtoProp1(),
            'demoObject2Prop' => $dtoProp2 = new DtoProp2(),
            'demoObject3Prop' => $dto2 = new Dto2(['demoStringProp' => 'hello world']),
        ]);

        $data = $dto1->all(false);
        self::assertSame('foo', $data['demoStringProp']);
        self::assertSame(1, $data['demoIntProp']);
        self::assertSame(1.5, $data['demoFloatProp']);
        self::assertTrue($data['demoTrueProp']);
        self::assertFalse($data['demoFalseProp']);
        self::assertSame($dtoProp1, $data['demoObjectProp']);
        self::assertSame($dtoProp2, $data['demoObject2Prop']);
        self::assertSame($dto2, $data['demoObject3Prop']);
        self::assertTrue($data['demoMixedProp']);
    }

    #[Api([
        'zh-CN:title' => '默认忽略丢失的值',
    ])]
    public function testDefaultIgnoreMissingValues(): void
    {
        $dto1 = new Dto1([
            'demo_not_found' => 1,
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);

        self::assertSame('foo', $dto1->demoStringProp);
    }

    #[Api([
        'zh-CN:title' => 'strict 从数组创建严格的数据传输对象（不能忽略传入数据的丢失的值）',
    ])]
    public function testStrict(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage(
            'Public properties `demo_not_found` of data transfer object `Tests\\Support\\Fixtures\\Dto1` was not defined.'
        );
        Dto1::strict([
            'demo_not_found' => 1,
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);
    }

    #[Api([
        'zh-CN:title' => 'fromArray 从数组创建数据传输对象',
    ])]
    public function testFromArray(): void
    {
        $dto1 = Dto1::fromArray([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);

        $data = $dto1
            ->only(['demoIntProp', 'demoObject3Prop'])
            ->toArray()
        ;
        self::assertSame([
            'demo_int_prop' => 1,
            'demo_object3_prop' => [
                'demo_string_prop' => 'hello world',
            ],
        ], $data);
    }

    #[Api([
        'zh-CN:title' => 'only 设置白名单属性',
    ])]
    public function testOnly(): void
    {
        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);

        $data = $dto1
            ->only(['demoIntProp', 'demoObject3Prop'])
            ->toArray()
        ;
        self::assertSame([
            'demo_int_prop' => 1,
            'demo_object3_prop' => [
                'demo_string_prop' => 'hello world',
            ],
        ], $data);
    }

    #[Api([
        'zh-CN:title' => 'only 设置白名单属性，合并默认白名单属性',
    ])]
    public function testOnlyWithOnlyPropertys(): void
    {
        $dto1 = new DtoToArray([
            'demoStringProp' => 'hello',
            'demoIntProp' => 123456,
            'demoIntOrStringProp' => 45,
        ]);

        $data = $dto1->toArray();
        self::assertSame([
            'demo_int_prop' => 123456,
            'demo_int_or_string_prop' => 45,
        ], $data);

        $data = $dto1->only(['demoStringProp'])->toArray();
        self::assertSame([
            'demo_string_prop' => 'hello',
            'demo_int_prop' => 123456,
            'demo_int_or_string_prop' => 45,
        ], $data);
    }

    #[Api([
        'zh-CN:title' => 'only 设置白名单属性，覆盖默认白名单属性',
    ])]
    public function testOnlyWithOnlyPropertysOverrideProperty(): void
    {
        $dto1 = new DtoToArray([
            'demoStringProp' => 'hello',
            'demoIntProp' => 123456,
            'demoIntOrStringProp' => 45,
        ]);

        $data = $dto1->toArray();
        self::assertSame([
            'demo_int_prop' => 123456,
            'demo_int_or_string_prop' => 45,
        ], $data);

        $data = $dto1->only(['demoStringProp'], true)->toArray();
        self::assertSame([
            'demo_string_prop' => 'hello',
        ], $data);
    }

    #[Api([
        'zh-CN:title' => 'except 设置黑名单属性',
    ])]
    public function testExcept(): void
    {
        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);

        $data = $dto1
            ->except(
                ['demoIntProp', 'demoObject3Prop', 'demoObjectProp',
                    'demoObject3Prop', 'demo_false_prop', 'demo_object2_prop', ]
            )
            ->toArray()
        ;
        self::assertSame([
            'demo_string_prop' => 'foo',
            'demo_float_prop' => 1.5,
            'demo_true_prop' => true,
            'demo_mixed_prop' => true,
        ], $data);
    }

    #[Api([
        'zh-CN:title' => 'except 设置黑名单属性，合并默认黑名单属性',
    ])]
    public function testExceptWithExceptPropertys(): void
    {
        $dto1 = new DtoToArray2([
            'demoStringProp' => 'hello',
            'demoIntProp' => 123456,
            'demoIntOrStringProp' => 45,
        ]);

        $data = $dto1->toArray();
        self::assertSame([
            'demo_string_prop' => 'hello',
        ], $data);

        $data = $dto1->except(['demoStringProp'])->toArray();
        self::assertSame([], $data);
    }

    #[Api([
        'zh-CN:title' => 'except 设置黑名单属性，覆盖默认黑名单属性',
    ])]
    public function testExceptWithExceptPropertysOverrideProperty(): void
    {
        $dto1 = new DtoToArray2([
            'demoStringProp' => 'hello',
            'demoIntProp' => 123456,
            'demoIntOrStringProp' => 45,
        ]);

        $data = $dto1->toArray();
        self::assertSame([
            'demo_string_prop' => 'hello',
        ], $data);

        $data = $dto1->except(['demo_int_prop'], true)->toArray();
        self::assertSame([
            'demo_string_prop' => 'hello',
            'demo_int_or_string_prop' => 45,
        ], $data);
    }

    #[Api([
        'zh-CN:title' => 'withoutNull 设置转换数组时忽略 NULL 值',
    ])]
    public function testWithoutNull(): void
    {
        $dto1 = new DtoToArray3([
            'demoStringProp' => 'hello',
            'demoIntProp' => 123456,
        ]);
        $data = $dto1->toArray();
        self::assertSame([
            'demo_string_prop' => 'hello',
            'demo_int_prop' => 123456,
            'demo_optional_int_prop' => null,
        ], $data);

        $data = $dto1->withoutNull()->toArray();
        self::assertSame([
            'demo_string_prop' => 'hello',
            'demo_int_prop' => 123456,
        ], $data);
    }

    #[Api([
        'zh-CN:title' => 'toArray 对象转数组（下划线属性命名风格）',
    ])]
    public function testToArrayUnCamelizeStyle(): void
    {
        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => $dtoProp2 = new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);

        $data = $dto1->toArray();
        self::assertSame('foo', $data['demo_string_prop']);
        self::assertSame(1, $data['demo_int_prop']);
        self::assertSame(1.5, $data['demo_float_prop']);
        self::assertTrue($data['demo_true_prop']);
        self::assertFalse($data['demo_false_prop']);
        self::assertSame(['demo1' => 'hello', 'demo2' => 'world'], $data['demo_object_prop']);
        self::assertSame($dtoProp2, $data['demo_object2_prop']);
        self::assertSame(['demo_string_prop' => 'hello world'], $data['demo_object3_prop']);
        self::assertTrue($data['demo_mixed_prop']);
    }

    #[Api([
        'zh-CN:title' => 'toArray.camelizeNamingStyle 对象转数组（驼峰属性命名风格）',
    ])]
    public function testToArrayCamelizeStyle(): void
    {
        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => $dtoProp2 = new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);

        $data = $dto1->camelizeNamingStyle()->toArray();
        self::assertSame('foo', $data['demoStringProp']);
        self::assertSame(1, $data['demoIntProp']);
        self::assertSame(1.5, $data['demoFloatProp']);
        self::assertTrue($data['demoTrueProp']);
        self::assertFalse($data['demoFalseProp']);
        self::assertSame(['demo1' => 'hello', 'demo2' => 'world'], $data['demoObjectProp']);
        self::assertSame($dtoProp2, $data['demoObject2Prop']);
        self::assertSame(['demoStringProp' => 'hello world'], $data['demoObject3Prop']);
        self::assertTrue($data['demoMixedProp']);
    }

    #[Api([
        'zh-CN:title' => 'toArray 对象转数组带有白名单属性设置',
    ])]
    public function testToArrayWithOnlyPropertys(): void
    {
        $dto1 = new DtoToArray([
            'demoStringProp' => 'hello',
            'demoIntProp' => 123456,
            'demoIntOrStringProp' => 45,
        ]);

        $data = $dto1->toArray();
        self::assertSame([
            'demo_int_prop' => 123456,
            'demo_int_or_string_prop' => 45,
        ], $data);
    }

    #[Api([
        'zh-CN:title' => '数据传输对象属性数组访问 ArrayAccess.offsetExists 支持',
    ])]
    public function testOffsetExists(): void
    {
        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);

        self::assertTrue(isset($dto1['demo_string_prop']));
        self::assertTrue(isset($dto1['demoStringProp']));
    }

    #[Api([
        'zh-CN:title' => '数据传输对象属性数组访问 ArrayAccess.offsetSet 支持',
    ])]
    public function testOffsetSet(): void
    {
        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);

        $dto1['demo_string_prop'] = 'hello_world';
        self::assertSame('hello_world', $dto1['demo_string_prop']);
        self::assertSame('hello_world', $dto1['demoStringProp']);

        $dto1['demo_string_prop'] = 'hello_world2';
        self::assertSame('hello_world2', $dto1['demo_string_prop']);
        self::assertSame('hello_world2', $dto1['demoStringProp']);
    }

    #[Api([
        'zh-CN:title' => '数据传输对象属性数组访问 ArrayAccess.offsetGet 支持',
    ])]
    public function testOffsetGet(): void
    {
        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);

        self::assertSame('foo', $dto1['demo_string_prop']);
        self::assertSame('foo', $dto1['demoStringProp']);
    }

    #[Api([
        'zh-CN:title' => '数据传输对象属性数组访问 ArrayAccess.offsetUnset 支持',
    ])]
    public function testOffsetUnset(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Cannot assign null to property Tests\Support\Fixtures\Dto1::$demoStringProp of type string'
        );

        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);

        self::assertSame('foo', $dto1['demo_string_prop']);
        self::assertSame('foo', $dto1['demoStringProp']);

        unset($dto1['demo_string_prop']);
    }

    public function testToArrayCamelizeStyleButOnlyUnCamelize(): void
    {
        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => $dtoProp2 = new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);

        $data = $dto1
            ->camelizeNamingStyle()
            ->only(['demo_object3_prop'])
            ->toArray()
        ;

        self::assertSame(['demoObject3Prop' => ['demoStringProp' => 'hello world']], $data);
    }

    public function test1(): void
    {
        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);
        self::assertSame(1, $dto1->demoIntProp);
        $dto1->demoFloatProp = null;
        self::assertNull($dto1->demoFloatProp);
    }

    #[Api([
        'zh-CN:title' => '初始化默认值填充方法',
        'zh-CN:description' => <<<'EOT'
``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\Fixtures\Dto3::class)]}
```
EOT,
    ])]
    public function test2(): void
    {
        $dto = new Dto3();
        self::assertSame('hello world', $dto->demoStringProp);
    }

    #[Api([
        'zh-CN:title' => '初始化转换数据方法',
        'zh-CN:description' => <<<'EOT'
``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\Fixtures\Dto4::class)]}
```
EOT,
    ])]
    public function test3(): void
    {
        $dto = new Dto4(['demoStringProp' => 123456]);
        self::assertSame('123456', $dto->demoStringProp);
    }

    public function test4(): void
    {
        $dto = new Dto5(['demoStringProp' => null]);
        self::assertSame('hello world', $dto->demoStringProp);
    }

    #[Api([
        'zh-CN:title' => '初始化内置转换数据方法',
        'zh-CN:description' => <<<'EOT'
``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\Fixtures\Dto6::class)]}
```
EOT,
    ])]
    public function test5(): void
    {
        $dto = new Dto6([
            'int1' => 'string',
            'string2' => 123456,
            'float3' => 'world',
            'bool4' => 0,
            'array5' => 'hello',
        ]);
        self::assertSame(0, $dto->int1);
        self::assertSame('123456', $dto->string2);
        self::assertSame(0.0, $dto->float3);
        self::assertFalse($dto->bool4);
        self::assertSame(['hello'], $dto->array5);
    }

    public function test6(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage(
            'Public properties `notFound` of data transfer object `Tests\\Support\\Fixtures\\Dto5` was not defined.'
        );

        $dto = new Dto5(['demoStringProp' => null]);
        $dto['notFound'] = 5;
    }

    public function test7(): void
    {
        $dto = new Dto6([
            'int1' => 'string',
            'string2' => 123456,
            'float3' => 'world',
            'bool4' => 0,
            'array5' => 'hello',
        ]);
        self::assertSame(0, $dto->int1);
        self::assertSame('123456', $dto->string2);
        self::assertSame(0.0, $dto->float3);
        self::assertFalse($dto->bool4);
        self::assertSame(['hello'], $dto->array5);

        $dto->int1 = 5.5;
        self::assertSame(5, $dto->int1);
    }

    public function test8(): void
    {
        $dto = new Dto7([
            'int1' => 'string',
            'string2' => 123456,
            'float3' => 'world',
            'bool4' => 0,
            'array5' => 'hello',
        ]);
        self::assertSame(1, $dto->int1);
        self::assertSame('123456', $dto->string2);
        self::assertSame(0.0, $dto->float3);
        self::assertFalse($dto->bool4);
        self::assertSame(['hello'], $dto->array5);

        $dto->int1 = 5.5;
        self::assertSame(6, $dto->int1);
    }
}
