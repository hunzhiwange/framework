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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use stdClass;
use Tests\TestCase;

/**
 * @api(
 *     title="Validator.type",
 *     zh-CN:title="验证器.数据类型验证",
 *     path="component/validate/validator/type",
 *     description="
 * 数据类型验证底层核心为函数 `Leevel\Support\Type\type`，相对于 PHP 提供的 `gettype` 更加强大。
 * ",
 * )
 */
class TypeTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed $value
     *
     * @api(
     *     title="验证通过的数据",
     *     description="
     * 以下是通过的校验数据示例。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\TypeTest::class, 'baseUseProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     note="",
     * )
     */
    public function testBaseUse($value, string $type): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'type:'.$type,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider(): array
    {
        $testFile = __DIR__.'/../assert/test.txt';
        $resource = fopen($testFile, 'r');

        // 主要为 is_xxx 系列
        // https://www.php.net/manual/zh/function.is-array.php
        return [
            [true, 'bool'],
            [true, 'bool'],
            [1.5, 'double'],
            [6.00, 'double'],
            ['中国', 'string'],
            ['成都no1', 'string'],
            [['foo', 'bar'], 'array'],
            [['hello', 'world'], 'array'],
            [['hello', 'world'], 'array:string'],
            [['hello', 'world'], 'array:int:string'],
            [['hello' => 'world', 'world' => 'world'], 'array:string:string'],
            [new stdClass(), 'object'],
            [new Type1(), 'object'],
            [$resource, 'resource'],
            [null, 'NULL'],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed $value
     * @param mixed $type
     *
     * @api(
     *     title="未验证通过的数据",
     *     description="
     * 以下是未通过的校验数据示例。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\TypeTest::class, 'badProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     note="",
     * )
     */
    public function testBad($value, $type): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'type:'.$type,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider(): array
    {
        return [
            ['not numeric', 'errorType'],
            [[], 'errorType'],
            [new stdClass(), 'errorType'],
            [['foo', 'bar'], 'errorType'],
            [[1, 2], 'errorType'],
            ['tel:+1-816-555-1212', 'errorType'],
            ['foo', 'errorType'],
            ['bar', 'errorType'],
            ['urn:oasis:names:specification:docbook:dtd:xml:4.1.2', 'errorType'],
            ['world', 'errorType'],
            [null, 'errorType'],
            ['errorType', 1],
        ];
    }

    /**
     * @api(
     *     title="type 参数缺失",
     *     description="",
     *     note="",
     * )
     */
    public function testMissParam(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Missing the first element of param.'
        );

        $validate = new Validator(
            [
                'name' => '',
            ],
            [
                'name'     => 'type',
            ]
        );

        $validate->success();
    }
}

class Type1
{
}
