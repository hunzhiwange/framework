<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Di\Container;
use Leevel\Kernel\Utils\Api;
use Tests\Support\Fixtures\Enum1;
use Tests\Support\Fixtures\Enum2;
use Tests\Support\Fixtures\RealEnumInt;
use Tests\Support\Fixtures\RealEnumNoValue;
use Tests\Support\Fixtures\RealEnumString;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '枚举',
    'path' => 'component/support/enum',
    'zh-CN:description' => <<<'EOT'
QueryPHP 提供了一个简单的枚举组件。
EOT,
])]
final class EnumTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        $container = Container::singletons();
        $container->clear();

        $container->singleton('i18n', static function (): \I18nMock {
            return new \I18nMock();
        });
    }

    #[\Override]
    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    #[Api([
        'zh-CN:title' => 'description 获取枚举值对应的描述',
    ])]
    public function testDescription(): void
    {
        self::assertSame('错误类型一', Enum1::description(Enum1::ERROR_ONE));
        self::assertSame('自定义错误', Enum1::description(Enum1::CUSTOM_ERROR));
        self::assertSame('错误类型一', Enum1::description(Enum1::ERROR_ONE));
        self::assertSame('自定义错误', Enum1::description(Enum1::CUSTOM_ERROR));
        self::assertSame('Status disabled', Enum1::description(Enum1::STATUS_DISABLE, 'status'));
        self::assertSame('Type enabled', Enum1::description(Enum1::TYPE_ENABLE, 'type'));
        self::assertSame('Type bool true', Enum1::description(Enum1::TYPE_BOOL_TRUE, 'type'));
        self::assertSame('Type bool false', Enum1::description(Enum1::TYPE_BOOL_FALSE, 'type'));
        self::assertSame('Type int', Enum1::description(Enum1::TYPE_INT, 'type'));
        self::assertSame('Type string float', Enum1::description(Enum1::TYPE_STRING_FLOAT, 'type'));
        self::assertSame('Type string', Enum1::description(Enum1::TYPE_STRING, 'type'));
        self::assertSame('Type null', Enum1::description(Enum1::TYPE_NULL, 'type'));
    }

    #[Api([
        'zh-CN:title' => '未设置注解将会被忽略',
    ])]
    public function testDescriptionButNoAttributes(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'Value `100013` is not part of Tests\\Support\\Fixtures\\Enum1:Leevel\\Support\\Msg'
        );

        Enum1::description(Enum1::NO_ATTRIBUTES);
    }

    #[Api([
        'zh-CN:title' => '注解为指定描述则为空',
    ])]
    public function testDescriptionButAttributesDescriptionNotFound(): void
    {
        self::assertSame('', Enum1::description(Enum1::NO_MSG));
    }

    #[Api([
        'zh-CN:title' => '值不存在枚举中会抛出异常',
    ])]
    public function testDescriptionButValueNotFound(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'Value `999999999999999` is not part of Tests\\Support\\Fixtures\\Enum1:Leevel\\Support\\Msg'
        );

        self::assertSame('', Enum1::description(999999999999999));
    }

    #[Api([
        'zh-CN:title' => '相同枚举值会匹配第一个',
        'zh-CN:description' => <<<'EOT'
基于 array_search 查找，第一个会被找到并返回。
EOT,
    ])]
    public function testDescriptionSameValueDescriptionWillBeFristOne(): void
    {
        self::assertSame('相同错误1', Enum1::description(Enum1::SAME_ERROR1));
        self::assertSame('相同错误1', Enum1::description(Enum1::SAME_ERROR2));
    }

    #[Api([
        'zh-CN:title' => 'descriptions 获取全部分组枚举描述',
    ])]
    public function testDescriptions(): void
    {
        $value = Enum1::descriptions('');
        $json = <<<'eot'
            {
                "Leevel\\Support\\Msg": {
                    "value": {
                        "ERROR_ONE": 100010,
                        "CUSTOM_ERROR": 100011,
                        "NO_MSG": 100014,
                        "PARAMS_INVALID": 100015,
                        "SAME_ERROR1": 100016,
                        "SAME_ERROR2": 100016
                    },
                    "description": {
                        "ERROR_ONE": "错误类型一",
                        "CUSTOM_ERROR": "自定义错误",
                        "NO_MSG": "",
                        "PARAMS_INVALID": "Hello %s world",
                        "SAME_ERROR1": "相同错误1",
                        "SAME_ERROR2": "相同错误2"
                    }
                },
                "status": {
                    "value": {
                        "STATUS_ENABLE": 1,
                        "STATUS_DISABLE": 0
                    },
                    "description": {
                        "STATUS_ENABLE": "Status enabled",
                        "STATUS_DISABLE": "Status disabled"
                    }
                },
                "accounts_type": {
                    "value": {
                        "ACCOUNTS_TYPE_MANAGER": "manager",
                        "ACCOUNTS_TYPE_SUPPLIER": "supplier",
                        "ACCOUNTS_TYPE_AGENCY": "agency"
                    },
                    "description": {
                        "ACCOUNTS_TYPE_MANAGER": "管理员账号",
                        "ACCOUNTS_TYPE_SUPPLIER": "供应商账号",
                        "ACCOUNTS_TYPE_AGENCY": "经销商账号"
                    }
                },
                "type": {
                    "value": {
                        "TYPE_ENABLE": 1,
                        "TYPE_DISABLE": 0,
                        "TYPE_BOOL_TRUE": true,
                        "TYPE_BOOL_FALSE": false,
                        "TYPE_INT": 11,
                        "TYPE_FLOAT": 1.1,
                        "TYPE_STRING_FLOAT": "1.1",
                        "TYPE_STRING": "string",
                        "TYPE_NULL": null
                    },
                    "description": {
                        "TYPE_ENABLE": "Type enabled",
                        "TYPE_DISABLE": "Type disabled",
                        "TYPE_BOOL_TRUE": "Type bool true",
                        "TYPE_BOOL_FALSE": "Type bool false",
                        "TYPE_INT": "Type int",
                        "TYPE_FLOAT": "Type float",
                        "TYPE_STRING_FLOAT": "Type string float",
                        "TYPE_STRING": "Type string",
                        "TYPE_NULL": "Type null"
                    }
                }
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $value
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'values 获取分组枚举值',
    ])]
    public function testValues(): void
    {
        $value = Enum1::values('status');
        $json = <<<'eot'
            [
                1,
                0
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $value
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'valueDescriptionMap 获取分组枚举值和描述映射',
    ])]
    public function testValueDescriptionMap(): void
    {
        $value = Enum1::valueDescription();
        $json = <<<'eot'
            {
                "100010": "错误类型一",
                "100011": "自定义错误",
                "100014": "",
                "100015": "Hello %s world",
                "100016": "相同错误2"
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $value
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'valueDescriptionMap 获取分组枚举值和描述映射',
    ])]
    public function test1(): void
    {
        $value = Enum2::valueDescription();
        $json = <<<'eot'
            {
                "100010": "错误类型我是:谁"
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $value
            )
        );

        self::assertSame('错误类型我是:谁', Enum2::description(Enum2::ERROR_ONE));
    }

    public function testRealEnumValueIntDescriptionMap(): void
    {
        $value = RealEnumInt::valueDescription();
        $json = <<<'eot'
            {
                "1": "未完成",
                "2": "已完成"
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $value
            )
        );
    }

    public function testRealEnumValueStringDescriptionMap(): void
    {
        $value = RealEnumString::valueDescription();
        $json = <<<'eot'
            {
                "hello": "世界",
                "world": "你好"
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $value
            )
        );
    }

    public function testRealEnumValueNoValueDescriptionMap(): void
    {
        $value = RealEnumNoValue::valueDescription();
        $json = <<<'eot'
            [
                "启用",
                "禁用"
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $value
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'descriptions 获取指定分组枚举描述',
    ])]
    public function testGetOneGroupDescriptions(): void
    {
        $value = Enum1::descriptions('status');
        $json = <<<'eot'
            {
                "value": {
                    "STATUS_ENABLE": 1,
                    "STATUS_DISABLE": 0
                },
                "description": {
                    "STATUS_ENABLE": "Status enabled",
                    "STATUS_DISABLE": "Status disabled"
                }
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $value
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'descriptions 获取指定分组枚举描述不存在将抛出异常',
    ])]
    public function testGetOneGroupDescriptionsButNotFound(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'Group `not_found` is not part of Tests\\Support\\Fixtures\\Enum1'
        );

        Enum1::descriptions('not_found');
    }

    #[Api([
        'zh-CN:title' => 'description 验证是否为有效的枚举值',
    ])]
    public function testIsValid(): void
    {
        self::assertTrue(Enum1::isValid(Enum1::ERROR_ONE));
        self::assertTrue(Enum1::isValid(Enum1::ERROR_ONE));
        self::assertFalse(Enum1::isValid(9999999));
        self::assertFalse(RealEnumInt::isValid('hello'));
    }

    #[Api([
        'zh-CN:title' => 'isValidKey 验证是否为有效的键',
    ])]
    public function testIsValidKey(): void
    {
        self::assertTrue(Enum1::isValidKey('ERROR_ONE'));
        self::assertFalse(Enum1::isValidKey('NOT_FOUND'));
        self::assertFalse(RealEnumInt::isValidKey('NOT_FOUND'));
        self::assertTrue(RealEnumInt::isValidKey('TRUE'));
    }

    #[Api([
        'zh-CN:title' => 'valueByKey 根据键值获取值',
    ])]
    public function testValueByKey(): void
    {
        self::assertSame(100010, Enum1::valueByKey('ERROR_ONE'));
        self::assertSame(100011, Enum1::valueByKey('CUSTOM_ERROR'));
        self::assertSame(1, RealEnumInt::valueByKey('FALSE'));
        self::assertSame(2, RealEnumInt::valueByKey('TRUE'));
    }

    public function testValueByKey1(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'Key `ERROR_ONE_NOT_FOUND` is not part of Tests\\Support\\Fixtures\\Enum1'
        );

        self::assertSame(100010, Enum1::valueByKey('ERROR_ONE_NOT_FOUND'));
    }

    public function testValueByKey2(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'Key `ERROR_ONE_NOT_FOUND` is not part of Tests\\Support\\Fixtures\\RealEnumInt'
        );

        self::assertSame(100010, RealEnumInt::valueByKey('ERROR_ONE_NOT_FOUND'));
    }

    public function testValueByKey3(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'Key `ENABLE` of Tests\\Support\\Fixtures\\RealEnumNoValue does not have a value'
        );

        self::assertSame(100010, RealEnumNoValue::valueByKey('ENABLE'));
    }

    #[Api([
        'zh-CN:title' => 'searchKey 获取给定值的键',
    ])]
    public function testSearchKey(): void
    {
        self::assertSame('ERROR_ONE', Enum1::searchKey(Enum1::ERROR_ONE));
        self::assertFalse(Enum1::searchKey(88));
        self::assertFalse(RealEnumInt::searchKey('hello'));
        self::assertSame('FALSE', RealEnumInt::searchKey(1));
    }

    #[Api([
        'zh-CN:title' => 'value 获取描述对应的枚举值',
    ])]
    public function testValue(): void
    {
        self::assertSame(100010, Enum1::value('错误类型一'));
        self::assertSame(1, RealEnumInt::value('未完成')->value);
        self::assertSame('FALSE', RealEnumInt::value('未完成')->name);
    }

    public function testValue1(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'Description `错误类型一 not found` is not part of Tests\\Support\\Fixtures\\Enum1:Leevel\\Support\\Msg'
        );

        Enum1::value('错误类型一 not found');
    }

    public function testValue2(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'Description `错误类型一 not found` is not part of Tests\\Support\\Fixtures\\RealEnumInt:Leevel\\Support\\Msg'
        );

        RealEnumInt::value('错误类型一 not found');
    }

    #[Api([
        'zh-CN:title' => 'names 获取分组枚举名字',
    ])]
    public function testNames(): void
    {
        $value = <<<'eot'
[
    "ERROR_ONE",
    "CUSTOM_ERROR",
    "NO_MSG",
    "PARAMS_INVALID",
    "SAME_ERROR1",
    "SAME_ERROR2"
]
eot;
        self::assertSame(
            $value,
            $this->varJson(
                Enum1::names()
            )
        );

        $value = <<<'eot'
[
    "FALSE",
    "TRUE"
]
eot;
        self::assertSame(
            $value,
            $this->varJson(
                RealEnumInt::names()
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'description 获取真实枚举值对应的描述',
    ])]
    public function testRealEnumDescription(): void
    {
        self::assertSame('已完成', RealEnumInt::description(RealEnumInt::TRUE));
        self::assertSame('未完成', RealEnumInt::description(RealEnumInt::FALSE));
        self::assertSame('世界', RealEnumString::description(RealEnumString::HELLO));
        self::assertSame('你好', RealEnumString::description(RealEnumString::WORLD));
        self::assertSame('启用', RealEnumNoValue::description(RealEnumNoValue::ENABLE));
        self::assertSame('禁用', RealEnumNoValue::description(RealEnumNoValue::DISABLE));
    }

    public function test3(): void
    {
        self::assertTrue(RealEnumInt::isValid('1'));
        self::assertFalse(RealEnumInt::isValid('hello world'));
    }
}
