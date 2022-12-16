<?php

declare(strict_types=1);

namespace Tests\Support;

use Tests\Support\Fixtures\Enum1;
use Tests\Support\Fixtures\StatusEnum;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="枚举",
 *     path="component/support/enum",
 *     zh-CN:description="QueryPHP 提供了一个简单的枚举组件。",
 * )
 */
class EnumTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="description 获取枚举值对应的描述",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testDescription(): void
    {
        $this->assertSame('错误类型一', Enum1::description(Enum1::ERROR_ONE));
        $this->assertSame('自定义错误', Enum1::description(Enum1::CUSTOM_ERROR));
        $this->assertSame('错误类型一', Enum1::description(Enum1::ERROR_ONE, 'msg'));
        $this->assertSame('自定义错误', Enum1::description(Enum1::CUSTOM_ERROR, 'msg'));
        $this->assertSame('Status disabled', Enum1::description(Enum1::STATUS_DISABLE, 'status'));
        $this->assertSame('Type enabled', Enum1::description(Enum1::TYPE_ENABLE, 'type'));
        $this->assertSame('Type bool true', Enum1::description(Enum1::TYPE_BOOL_TRUE, 'type'));
        $this->assertSame('Type bool false', Enum1::description(Enum1::TYPE_BOOL_FALSE, 'type'));
        $this->assertSame('Type int', Enum1::description(Enum1::TYPE_INT, 'type'));
        $this->assertSame('Type string float', Enum1::description(Enum1::TYPE_STRING_FLOAT, 'type'));
        $this->assertSame('Type string', Enum1::description(Enum1::TYPE_STRING, 'type'));
        $this->assertSame('Type null', Enum1::description(Enum1::TYPE_NULL, 'type'));
    }

    /**
     * @api(
     *     zh-CN:title="未设置注解将会被忽略",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testDescriptionButNoAttributes(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'Value `100013` is not part of Tests\\Support\\Fixtures\\Enum1:msg'
        );

        Enum1::description(Enum1::NO_ATTRIBUTES);
    }

    /**
     * @api(
     *     zh-CN:title="注解为指定描述则为空",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testDescriptionButAttributesDescriptionNotFound(): void
    {
        $this->assertSame('', Enum1::description(Enum1::NO_MSG));
    }

    /**
     * @api(
     *     zh-CN:title="值不存在枚举中会抛出异常",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testDescriptionButValueNotFound(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'Value `999999999999999` is not part of Tests\\Support\\Fixtures\\Enum1:msg'
        );

        $this->assertSame('', Enum1::description(999999999999999));
    }

    /**
     * @api(
     *     zh-CN:title="相同枚举值会匹配第一个",
     *     zh-CN:description="基于 array_search 查找，第一个会被找到并返回。",
     *     zh-CN:note="",
     * )
     */
    public function testDescriptionSameValueDescriptionWillBeFristOne(): void
    {
        $this->assertSame('相同错误1', Enum1::description(Enum1::SAME_ERROR1));
        $this->assertSame('相同错误1', Enum1::description(Enum1::SAME_ERROR2));
    }

    /**
     * @api(
     *     zh-CN:title="descriptions 获取全部分组枚举描述",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testDescriptions(): void
    {
        $value = Enum1::descriptions();
        $json = <<<'eot'
            {
                "msg": {
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

        $this->assertSame(
            $json,
            $this->varJson(
                $value
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="values 获取分组枚举值",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testValues(): void
    {
        $value = Enum1::values('status');
        $json = <<<'eot'
            [
                1,
                0
            ]
            eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $value
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="descriptions 获取指定分组枚举描述",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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

        $this->assertSame(
            $json,
            $this->varJson(
                $value
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="descriptions 获取指定分组枚举描述不存在将抛出异常",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testGetOneGroupDescriptionsButNotFound(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'Group `not_found` is not part of Tests\\Support\\Fixtures\\Enum1'
        );

        Enum1::descriptions('not_found');
    }

    /**
     * @api(
     *     zh-CN:title="description 验证是否为有效的枚举值",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testIsValid(): void
    {
        $this->assertTrue(Enum1::isValid(Enum1::ERROR_ONE));
        $this->assertTrue(Enum1::isValid(Enum1::ERROR_ONE, 'msg'));
        $this->assertFalse(Enum1::isValid(9999999));
    }

    /**
     * @api(
     *     zh-CN:title="isValidKey 验证是否为有效的键",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testIsValidKey(): void
    {
        $this->assertTrue(Enum1::isValidKey('ERROR_ONE'));
        $this->assertFalse(Enum1::isValidKey('NOT_FOUND'));
    }

    /**
     * @api(
     *     zh-CN:title="searchKey 获取给定值的键",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSearchKey(): void
    {
        $this->assertSame('ERROR_ONE', Enum1::searchKey(Enum1::ERROR_ONE));
        $this->assertSame(false, Enum1::searchKey(88));
    }
}
