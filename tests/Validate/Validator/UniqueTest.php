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

use Leevel\Di\Container;
use Leevel\Validate\IValidator;
use Leevel\Validate\UniqueRule;
use Leevel\Validate\Validator;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\CompositeId;
use Tests\Database\Ddd\Entity\Guestbook;

/**
 * @api(
 *     title="Validator.unique",
 *     zh-CN:title="验证器.是否可接受的",
 *     path="component/validate/validator/unique",
 *     description="",
 * )
 */
class UniqueTest extends TestCase
{
    /**
     * @api(
     *     title="唯一值基本使用方法",
     *     description="
     * 框架提供了一个唯一值创建生成规则方法
     *
     * ``` php
     * \Leevel\Validate\UniqueRule::rule(string $entity, ?string $field = null, $exceptId = null, ?string $primaryKey = null, ...$additional): string;
     * ```
     *
     *   * entity 实体
     *   * field 指定数据库字段，未指定默认为待验证的字段作为数据库字段
     *   * exceptId 排除主键，一般用于编辑数据项校验
     *   * primaryKey 指定主键
     *   * additional 附加查询条件，成对出现
     *
     * 唯一值是一个非常常用的功能，框架强化了这一功能。
     * ",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, null, 1),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,__int@1,_', $rule);
        $this->assertTrue($validate->success());

        $sql = $this->getLastSql('guest_book');
        $this->assertSame($sql, "SQL: [115] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> 1 LIMIT 1 | Params:  0");
    }

    /**
     * @api(
     *     title="排除主键",
     *     description="",
     *     note="",
     * )
     */
    public function testValidateWithExceptId(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, null, 1),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,__int@1,_', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $this->assertSame($sql, "SQL: [115] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> 1 LIMIT 1 | Params:  0");

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert([
                    'name'    => 'foo',
                    'content' => '',
                ]),
        );

        $this->assertTrue($validate->success());
    }

    /**
     * @api(
     *     title="排除主键，并且指定主键",
     *     description="",
     *     note="",
     * )
     */
    public function testValidateWithExceptIdAndPrimaryKey(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, null, 1, 'id'),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,__int@1,id', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $this->assertSame($sql, "SQL: [115] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> 1 LIMIT 1 | Params:  0");

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert([
                    'name'    => 'foo',
                    'content' => '',
                ]),
        );

        $this->assertTrue($validate->success());
    }

    /**
     * @api(
     *     title="排除主键，复合主键将会被忽略",
     *     description="",
     *     note="",
     * )
     */
    public function testValidateWithExceptIdAndCompositeIdAndIgnore(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(CompositeId::class, null, 1),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\CompositeId,_,__int@1,_', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('composite_id');
        $this->assertSame($sql, "SQL: [92] SELECT COUNT(*) AS row_count FROM `composite_id` WHERE `composite_id`.`name` = 'foo' LIMIT 1 | Params:  0");

        $connect = $this->createDatabaseConnect();

        $connect
            ->table('composite_id')
            ->insert([
                'id1'   => 1,
                'id2'   => 2,
                'name'  => '',
            ]);

        $this->assertTrue($validate->success());
    }

    /**
     * @api(
     *     title="不排除主键",
     *     description="",
     *     note="",
     * )
     */
    public function testValidateWithoutExceptId(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, null),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,_,_', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $this->assertSame($sql, "SQL: [88] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' LIMIT 1 | Params:  0");

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
             1,
            $connect
                ->table('guest_book')
                ->insert([
                    'name'    => 'foo',
                    'content' => '',
                ]),
        );

        $this->assertFalse($validate->success());
    }

    /**
     * @api(
     *     title="unique 参数缺失",
     *     description="",
     *     note="",
     * )
     */
    public function testCheckParamLengthException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Missing the first element of param.'
        );

        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => 'unique',
            ]
        );

        $validate->success();
    }

    public function testAdditionalConditionsMustBeStringException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unique additional conditions must be scalar type.'
        );

        new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, null, 1, null, ['arr']),
            ]
        );
    }

    public function testEntityNotFoundException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Validate entity `Test\\Validate\\NotFoundEntity` was not found.'
        );

        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule('Test\\Validate\\NotFoundEntity', null, 1),
            ]
        );

        $validate->success();
    }

    public function testValidateArgsNotObjectAndNotStringWillReturnFalse(): void
    {
        $rule = new UniqueRule();
        $this->assertFalse($rule->validate('value', [['arr']], $this->createMock(IValidator::class), 'field'));
    }

    public function testValidateArgsIsEntity(): void
    {
        $rule = new UniqueRule();

        $this->assertTrue($rule->validate('value', [new Guestbook()], $this->createMock(IValidator::class), 'name'));

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert([
                    'name'    => 'value',
                    'content' => '',
                ]),
        );

        $this->assertFalse($rule->validate('value', [new Guestbook()], $this->createMock(IValidator::class), 'name'));
    }

    public function testValidateArgsIsObjectButNotIsEntity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Validate entity `Tests\\Validate\\Validator\\DemoUnique1` must be an entity.'
        );

        $rule = new UniqueRule();

        $rule->validate('value', [new DemoUnique1()], $this->createMock(IValidator::class), 'name');
    }

    public function testValidateArgsIsStringButNotIsEntity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Validate entity `Tests\\Validate\\Validator\\DemoUnique1` must be an entity.'
        );

        $rule = new UniqueRule();

        $rule->validate('value', ['Tests\\Validate\\Validator\\DemoUnique1'], $this->createMock(IValidator::class), 'name');
    }

    /**
     * @api(
     *     title="指定验证数据库字段",
     *     description="",
     *     note="",
     * )
     */
    public function testValidateWithValidateField(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, 'name', 1),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,name,__int@1,_', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $this->assertSame($sql, "SQL: [115] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> 1 LIMIT 1 | Params:  0");

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert([
                    'name'    => 'foo',
                    'content' => '',
                ]),
        );

        $this->assertTrue($validate->success());
    }

    /**
     * @api(
     *     title="指定验证数据库字段，支持多个字段",
     *     description="",
     *     note="",
     * )
     */
    public function testValidateWithValidateMultiField(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, 'name:content', 1),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,name:content,__int@1,_', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $this->assertSame($sql, "SQL: [150] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`content` = 'foo' AND `guest_book`.`id` <> 1 LIMIT 1 | Params:  0");

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert([
                    'name'    => 'foo',
                    'content' => '',
                ]),
        );

        $this->assertTrue($validate->success());
    }

    /**
     * @api(
     *     title="带附加条件",
     *     description="",
     *     note="",
     * )
     */
    public function testValidateWithParseAdditional(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, null, null, null, 'id', '1'),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,_,_,id,__string@1', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $this->assertSame($sql, "SQL: [116] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` = '1' LIMIT 1 | Params:  0");

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert([
                    'name'    => 'foo',
                    'content' => '',
                ]),
        );

        $this->assertFalse($validate->success());
    }

    public function testValidateWithParseAdditional2(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, null, null, null, 'content', 'hello'),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,_,_,content,__string@hello', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $this->assertSame($sql, "SQL: [125] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`content` = 'hello' LIMIT 1 | Params:  0");

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert([
                    'name'    => 'foo',
                    'content' => 'hello',
                ]),
        );

        $this->assertFalse($validate->success());
    }

    public function testValidateWithParseAdditional3(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, null, null, null, 'content', 'hello'),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,_,_,content,__string@hello', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $this->assertSame($sql, "SQL: [125] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`content` = 'hello' LIMIT 1 | Params:  0");

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert([
                    'name'    => 'foo',
                    'content' => 'world',
                ]),
        );

        $this->assertTrue($validate->success());
    }

    /**
     * @api(
     *     title="带附加条件，附加条件支持表达式",
     *     description="",
     *     note="",
     * )
     */
    public function testValidateWithParseAdditionalCustomOperate(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, null, null, null, 'id:>', '1'),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,_,_,id:>,__string@1', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $this->assertSame($sql, "SQL: [116] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` > '1' LIMIT 1 | Params:  0");

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert([
                    'name'    => 'foo',
                    'content' => '',
                ]),
        );

        $this->assertTrue($validate->success());
    }

    public function testValidateWithParseAdditionalMustBePairedException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unique additional conditions must be paired.'
        );

        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, null, null, null, 'id'),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,_,_,id', $rule);
        $validate->success();
    }

    public function testWithContainer(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, null, 1),
            ]
        );

        $validate->setContainer(new Container());

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,__int@1,_', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $this->assertSame($sql, "SQL: [115] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> 1 LIMIT 1 | Params:  0");
    }

    public function testWithPlaceHolder(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(
                    Guestbook::class,
                    UniqueRule::PLACEHOLDER,
                    UniqueRule::PLACEHOLDER,
                    UniqueRule::PLACEHOLDER,
                    'content',
                    'hello',
                ),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,__string@_,_,content,__string@hello', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $this->assertSame($sql, "SQL: [154] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> '_' AND `guest_book`.`content` = 'hello' LIMIT 1 | Params:  0");

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert([
                    'name'    => 'foo',
                    'content' => 'hello',
                ]),
        );

        $this->assertFalse($validate->success());
    }

    /**
     * @api(
     *     title="带附加条件，附加条件区分整数和浮点数的字符串",
     *     description="",
     *     note="",
     * )
     */
    public function testValidateWithStringFloatAndStringInt(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, 'name', '1', null, 'content', '1.5'),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,name,__string@1,_,content,__string@1.5', $rule);
        $this->assertTrue($validate->success());

        $sql = $this->getLastSql('guest_book');
        $this->assertSame($sql, "SQL: [152] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> '1' AND `guest_book`.`content` = '1.5' LIMIT 1 | Params:  0");
    }

    /**
     * @api(
     *     title="带附加条件，附加条件为整数和浮点数",
     *     description="",
     *     note="",
     * )
     */
    public function testValidateWithFloatAndInt(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, 'name', 1, null, 'content', 1.5),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,name,__int@1,_,content,__float@1.5', $rule);
        $this->assertTrue($validate->success());

        $sql = $this->getLastSql('guest_book');
        $this->assertSame($sql, "SQL: [148] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> 1 AND `guest_book`.`content` = 1.5 LIMIT 1 | Params:  0");
    }

    public function testValidateValueIsNotString(): void
    {
        $rule = new UniqueRule();
        $this->assertTrue($rule->validate(3, [Guestbook::class], $this->createMock(IValidator::class), 'id'));
        $this->assertTrue($rule->validate(1.5, [Guestbook::class], $this->createMock(IValidator::class), 'id'));
    }

    protected function getDatabaseTable(): array
    {
        return ['guest_book', 'composite_id'];
    }
}

class DemoUnique1
{
}
