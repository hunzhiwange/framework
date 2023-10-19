<?php

declare(strict_types=1);

namespace Tests\Support;

use Tests\Support\Fixtures\Dto1;
use Tests\Support\Fixtures\Dto2;
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
        static::assertSame('foo', $data['demo_string_prop']);
        static::assertSame(1, $data['demo_int_prop']);
        static::assertSame(1.5, $data['demo_float_prop']);
        static::assertTrue($data['demo_true_prop']);
        static::assertFalse($data['demo_false_prop']);
        static::assertSame($dtoProp1, $data['demo_object_prop']);
        static::assertSame($dtoProp2, $data['demo_object2_prop']);
        static::assertSame($dto2, $data['demo_object3_prop']);
        static::assertTrue($data['demo_mixed_prop']);
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
        static::assertSame('foo', $data['demoStringProp']);
        static::assertSame(1, $data['demoIntProp']);
        static::assertSame(1.5, $data['demoFloatProp']);
        static::assertTrue($data['demoTrueProp']);
        static::assertFalse($data['demoFalseProp']);
        static::assertSame($dtoProp1, $data['demoObjectProp']);
        static::assertSame($dtoProp2, $data['demoObject2Prop']);
        static::assertSame($dto2, $data['demoObject3Prop']);
        static::assertTrue($data['demoMixedProp']);
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

        static::assertSame('foo', $dto1->demo_string_prop);
    }

    #[Api([
        'zh-CN:title' => 'strict 从数组或者数据传输对象创建不可变数据传输对象',
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
        static::assertSame([
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
        static::assertSame([
            'demo_int_prop' => 123456,
            'demo_int_or_string_prop' => 45,
        ], $data);

        $data = $dto1->only(['demoStringProp'])->toArray();
        static::assertSame([
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
        static::assertSame([
            'demo_int_prop' => 123456,
            'demo_int_or_string_prop' => 45,
        ], $data);

        $data = $dto1->only(['demoStringProp'], true)->toArray();
        static::assertSame([
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
        static::assertSame([
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
        static::assertSame([
            'demo_string_prop' => 'hello',
        ], $data);

        $data = $dto1->except(['demoStringProp'])->toArray();
        static::assertSame([], $data);
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
        static::assertSame([
            'demo_string_prop' => 'hello',
        ], $data);

        $data = $dto1->except(['demo_int_prop'], true)->toArray();
        static::assertSame([
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
        static::assertSame([
            'demo_string_prop' => 'hello',
            'demo_int_prop' => 123456,
            'demo_optional_int_prop' => null,
        ], $data);

        $data = $dto1->withoutNull()->toArray();
        static::assertSame([
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
        static::assertSame('foo', $data['demo_string_prop']);
        static::assertSame(1, $data['demo_int_prop']);
        static::assertSame(1.5, $data['demo_float_prop']);
        static::assertTrue($data['demo_true_prop']);
        static::assertFalse($data['demo_false_prop']);
        static::assertSame(['demo1' => 'hello', 'demo2' => 'world'], $data['demo_object_prop']);
        static::assertSame($dtoProp2, $data['demo_object2_prop']);
        static::assertSame(['demo_string_prop' => 'hello world'], $data['demo_object3_prop']);
        static::assertTrue($data['demo_mixed_prop']);
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
        static::assertSame('foo', $data['demoStringProp']);
        static::assertSame(1, $data['demoIntProp']);
        static::assertSame(1.5, $data['demoFloatProp']);
        static::assertTrue($data['demoTrueProp']);
        static::assertFalse($data['demoFalseProp']);
        static::assertSame(['demo1' => 'hello', 'demo2' => 'world'], $data['demoObjectProp']);
        static::assertSame($dtoProp2, $data['demoObject2Prop']);
        static::assertSame(['demoStringProp' => 'hello world'], $data['demoObject3Prop']);
        static::assertTrue($data['demoMixedProp']);
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
        static::assertSame([
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

        static::assertTrue(isset($dto1['demo_string_prop']));
        static::assertTrue(isset($dto1['demoStringProp']));
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
        static::assertSame('hello_world', $dto1['demo_string_prop']);
        static::assertSame('hello_world', $dto1['demoStringProp']);

        $dto1['demo_string_prop'] = 'hello_world2';
        static::assertSame('hello_world2', $dto1['demo_string_prop']);
        static::assertSame('hello_world2', $dto1['demoStringProp']);
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

        static::assertSame('foo', $dto1['demo_string_prop']);
        static::assertSame('foo', $dto1['demoStringProp']);
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

        static::assertSame('foo', $dto1['demo_string_prop']);
        static::assertSame('foo', $dto1['demoStringProp']);

        unset($dto1['demo_string_prop']);
    }

    #[Api([
        'zh-CN:title' => '数据传输对象属性访问魔术方法 __isset 支持',
    ])]
    public function testMagicIsset(): void
    {
        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);
        static::assertTrue(isset($dto1->demo_string_prop));
        static::assertTrue(isset($dto1->demo_int_prop));
    }

    #[Api([
        'zh-CN:title' => '数据传输对象属性访问魔术方法 __set 支持',
    ])]
    public function testMagicSet(): void
    {
        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);

        static::assertSame('foo', $dto1->demo_string_prop);
        $dto1->demo_string_prop = 'hello';
        static::assertSame('hello', $dto1->demo_string_prop);
    }

    #[Api([
        'zh-CN:title' => '数据传输对象属性访问魔术方法 __get 支持',
    ])]
    public function testMagicGet(): void
    {
        $dto1 = new Dto1([
            'demo_string_prop' => 'foo',
            'demoIntProp' => 1,
            'demoFloatProp' => 1.5,
            'demoObjectProp' => new DtoProp1(),
            'demoObject2Prop' => new DtoProp2(),
            'demoObject3Prop' => new Dto2(['demoStringProp' => 'hello world']),
        ]);
        static::assertSame('foo', $dto1->demo_string_prop);
    }

    #[Api([
        'zh-CN:title' => '实体属性访问魔术方法 __unset 支持',
    ])]
    public function testMagicUnset(): void
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
        $dto1->demo_string_prop = null;
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

        static::assertSame(['demoObject3Prop' => ['demoStringProp' => 'hello world']], $data);
    }
}
