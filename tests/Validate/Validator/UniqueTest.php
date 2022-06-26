<?php

declare(strict_types=1);

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
 *     zh-CN:title="Validator.unique",
 *     zh-CN:title="验证器.是否可接受的",
 *     path="validate/validator/unique",
 *     zh-CN:description="",
 * )
 */
class UniqueTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="唯一值基本使用方法",
     *     zh-CN:description="
     * 框架提供了一个唯一值创建生成规则方法
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Validate\UniqueRule::class, 'rule', 'define')]}
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
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, exceptId:1),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,:int:1,_', $rule);
        $this->assertTrue($validate->success());

        $sql = $this->getLastSql('guest_book');
        $sqlResult = "SQL: [139] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = :guest_book_name AND `guest_book`.`id` <> :guest_book_id LIMIT 1 | Params:  2 | Key: Name: [16] :guest_book_name | paramno=0 | name=[16] \":guest_book_name\" | is_param=1 | param_type=2 | Key: Name: [14] :guest_book_id | paramno=1 | name=[14] \":guest_book_id\" | is_param=1 | param_type=1 (SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> 1 LIMIT 1)";
        $this->assertSame($sql, $sqlResult);
    }

    /**
     * @api(
     *     zh-CN:title="排除主键",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testValidateWithExceptId(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, exceptId:1),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,:int:1,_', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $sqlResult = "SQL: [139] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = :guest_book_name AND `guest_book`.`id` <> :guest_book_id LIMIT 1 | Params:  2 | Key: Name: [16] :guest_book_name | paramno=0 | name=[16] \":guest_book_name\" | is_param=1 | param_type=2 | Key: Name: [14] :guest_book_id | paramno=1 | name=[14] \":guest_book_id\" | is_param=1 | param_type=1 (SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> 1 LIMIT 1)";
        $this->assertSame($sql, $sqlResult);

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
     *     zh-CN:title="排除主键，并且指定主键",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,:int:1,id', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $sqlResult = "SQL: [139] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = :guest_book_name AND `guest_book`.`id` <> :guest_book_id LIMIT 1 | Params:  2 | Key: Name: [16] :guest_book_name | paramno=0 | name=[16] \":guest_book_name\" | is_param=1 | param_type=2 | Key: Name: [14] :guest_book_id | paramno=1 | name=[14] \":guest_book_id\" | is_param=1 | param_type=1 (SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> 1 LIMIT 1)";
        $this->assertSame($sql, $sqlResult);

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
     *     zh-CN:title="排除主键，复合主键将会被忽略",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testValidateWithExceptIdAndCompositeIdAndIgnore(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(CompositeId::class, exceptId:1),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\CompositeId,_,:int:1,_', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('composite_id');
        $sqlResult = "SQL: [105] SELECT COUNT(*) AS row_count FROM `composite_id` WHERE `composite_id`.`name` = :composite_id_name LIMIT 1 | Params:  1 | Key: Name: [18] :composite_id_name | paramno=0 | name=[18] \":composite_id_name\" | is_param=1 | param_type=2 (SELECT COUNT(*) AS row_count FROM `composite_id` WHERE `composite_id`.`name` = 'foo' LIMIT 1)";
        $this->assertSame($sql, $sqlResult);

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
     *     zh-CN:title="不排除主键",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testValidateWithoutExceptId(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,_,_', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $sqlResult = "SQL: [99] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = :guest_book_name LIMIT 1 | Params:  1 | Key: Name: [16] :guest_book_name | paramno=0 | name=[16] \":guest_book_name\" | is_param=1 | param_type=2 (SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' LIMIT 1)";
        $this->assertSame($sql, $sqlResult);

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
     *     zh-CN:title="unique 参数缺失",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

    public function testAdditionalConditionsMustBeStringScalarArrayTypeException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unique additional conditions must be `string:scalar` array.'
        );

        new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, null, 1, additional:[['arr']]),
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
                'name'     => UniqueRule::rule('Test\\Validate\\NotFoundEntity', exceptId:1),
            ]
        );

        $validate->success();
    }

    public function testValidateArgsNotObjectAndNotStringWillReturnFalse(): void
    {
        $rule = new UniqueRule();
        $this->assertFalse($rule->handle('value', [['arr']], $this->createMock(IValidator::class), 'field'));
    }

    public function testValidateArgsIsEntity(): void
    {
        $rule = new UniqueRule();

        $this->assertTrue($rule->handle('value', [new Guestbook()], $this->createMock(IValidator::class), 'name'));

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

        $this->assertFalse($rule->handle('value', [new Guestbook()], $this->createMock(IValidator::class), 'name'));
    }

    public function testValidateArgsIsObjectButNotIsEntity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Validate entity `Tests\\Validate\\Validator\\DemoUnique1` must be an entity.'
        );

        $rule = new UniqueRule();

        $rule->handle('value', [new DemoUnique1()], $this->createMock(IValidator::class), 'name');
    }

    public function testValidateArgsIsStringButNotIsEntity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Validate entity `Tests\\Validate\\Validator\\DemoUnique1` must be an entity.'
        );

        $rule = new UniqueRule();

        $rule->handle('value', ['Tests\\Validate\\Validator\\DemoUnique1'], $this->createMock(IValidator::class), 'name');
    }

    /**
     * @api(
     *     zh-CN:title="指定验证数据库字段",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,name,:int:1,_', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $sqlResult = "SQL: [139] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = :guest_book_name AND `guest_book`.`id` <> :guest_book_id LIMIT 1 | Params:  2 | Key: Name: [16] :guest_book_name | paramno=0 | name=[16] \":guest_book_name\" | is_param=1 | param_type=2 | Key: Name: [14] :guest_book_id | paramno=1 | name=[14] \":guest_book_id\" | is_param=1 | param_type=1 (SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> 1 LIMIT 1)";
        $this->assertSame($sql, $sqlResult);

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
     *     zh-CN:title="指定验证数据库字段，支持多个字段",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,name:content,:int:1,_', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $sqlResult = "SQL: [188] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = :guest_book_name AND `guest_book`.`content` = :guest_book_content AND `guest_book`.`id` <> :guest_book_id LIMIT 1 | Params:  3 | Key: Name: [16] :guest_book_name | paramno=0 | name=[16] \":guest_book_name\" | is_param=1 | param_type=2 | Key: Name: [19] :guest_book_content | paramno=1 | name=[19] \":guest_book_content\" | is_param=1 | param_type=2 | Key: Name: [14] :guest_book_id | paramno=2 | name=[14] \":guest_book_id\" | is_param=1 | param_type=1 (SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`content` = 'foo' AND `guest_book`.`id` <> 1 LIMIT 1)";
        $this->assertSame($sql, $sqlResult);

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
     *     zh-CN:title="带附加条件",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testValidateWithParseAdditional(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, additional:['id' => '1']),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,_,_,id,:string:1', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $sqlResult = "SQL: [138] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = :guest_book_name AND `guest_book`.`id` = :guest_book_id LIMIT 1 | Params:  2 | Key: Name: [16] :guest_book_name | paramno=0 | name=[16] \":guest_book_name\" | is_param=1 | param_type=2 | Key: Name: [14] :guest_book_id | paramno=1 | name=[14] \":guest_book_id\" | is_param=1 | param_type=2 (SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` = '1' LIMIT 1)";
        $this->assertSame($sql, $sqlResult);

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
                'name'     => $rule = UniqueRule::rule(Guestbook::class, additional:['content' => 'hello']),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,_,_,content,:string:hello', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $sqlResult = "SQL: [148] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = :guest_book_name AND `guest_book`.`content` = :guest_book_content LIMIT 1 | Params:  2 | Key: Name: [16] :guest_book_name | paramno=0 | name=[16] \":guest_book_name\" | is_param=1 | param_type=2 | Key: Name: [19] :guest_book_content | paramno=1 | name=[19] \":guest_book_content\" | is_param=1 | param_type=2 (SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`content` = 'hello' LIMIT 1)";
        $this->assertSame($sql, $sqlResult);

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
                'name'     => $rule = UniqueRule::rule(Guestbook::class, additional:['content' => 'hello']),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,_,_,content,:string:hello', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $sqlResult = "SQL: [148] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = :guest_book_name AND `guest_book`.`content` = :guest_book_content LIMIT 1 | Params:  2 | Key: Name: [16] :guest_book_name | paramno=0 | name=[16] \":guest_book_name\" | is_param=1 | param_type=2 | Key: Name: [19] :guest_book_content | paramno=1 | name=[19] \":guest_book_content\" | is_param=1 | param_type=2 (SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`content` = 'hello' LIMIT 1)";
        $this->assertSame($sql, $sqlResult);

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
     *     zh-CN:title="带附加条件，附加条件支持表达式",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testValidateWithParseAdditionalCustomOperate(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, additional:['id:>' => '1']),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,_,_,id:>,:string:1', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $sqlResult = "SQL: [138] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = :guest_book_name AND `guest_book`.`id` > :guest_book_id LIMIT 1 | Params:  2 | Key: Name: [16] :guest_book_name | paramno=0 | name=[16] \":guest_book_name\" | is_param=1 | param_type=2 | Key: Name: [14] :guest_book_id | paramno=1 | name=[14] \":guest_book_id\" | is_param=1 | param_type=2 (SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` > '1' LIMIT 1)";
        $this->assertSame($sql, $sqlResult);

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
                'name'     => 'unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,_,_,id',
            ]
        );

        $validate->success();
    }

    public function testWithContainer(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, exceptId:1),
            ]
        );

        $validate->setContainer(new Container());

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,:int:1,_', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $sqlResult = "SQL: [139] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = :guest_book_name AND `guest_book`.`id` <> :guest_book_id LIMIT 1 | Params:  2 | Key: Name: [16] :guest_book_name | paramno=0 | name=[16] \":guest_book_name\" | is_param=1 | param_type=2 | Key: Name: [14] :guest_book_id | paramno=1 | name=[14] \":guest_book_id\" | is_param=1 | param_type=1 (SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> 1 LIMIT 1)";
        $this->assertSame($sql, $sqlResult);
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
                    [
                        'content' => 'hello',
                    ],
                ),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,_,_,_,content,:string:hello', $rule);
        $this->assertTrue($validate->success());

        $sql = $this->getLastSql('guest_book');
        $sqlResult = "SQL: [148] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = :guest_book_name AND `guest_book`.`content` = :guest_book_content LIMIT 1 | Params:  2 | Key: Name: [16] :guest_book_name | paramno=0 | name=[16] \":guest_book_name\" | is_param=1 | param_type=2 | Key: Name: [19] :guest_book_content | paramno=1 | name=[19] \":guest_book_content\" | is_param=1 | param_type=2 (SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`content` = 'hello' LIMIT 1)";
        $this->assertSame($sql, $sqlResult);

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
     *     zh-CN:title="带附加条件，附加条件区分整数和浮点数的字符串",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testValidateWithStringFloatAndStringInt(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, 'name', '1', additional:['content' => '1.5']),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,name,:string:1,_,content,:string:1.5', $rule);
        $this->assertTrue($validate->success());

        $sql = $this->getLastSql('guest_book');
        $sqlResult = "SQL: [188] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = :guest_book_name AND `guest_book`.`id` <> :guest_book_id AND `guest_book`.`content` = :guest_book_content LIMIT 1 | Params:  3 | Key: Name: [16] :guest_book_name | paramno=0 | name=[16] \":guest_book_name\" | is_param=1 | param_type=2 | Key: Name: [14] :guest_book_id | paramno=1 | name=[14] \":guest_book_id\" | is_param=1 | param_type=2 | Key: Name: [19] :guest_book_content | paramno=2 | name=[19] \":guest_book_content\" | is_param=1 | param_type=2 (SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> '1' AND `guest_book`.`content` = '1.5' LIMIT 1)";
        $this->assertSame($sql, $sqlResult);
    }

    /**
     * @api(
     *     zh-CN:title="带附加条件，附加条件为整数和浮点数",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testValidateWithFloatAndInt(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => $rule = UniqueRule::rule(Guestbook::class, 'name', 1, additional:['content' => 1.5]),
            ]
        );

        $this->assertSame('unique:Tests\\Database\\Ddd\\Entity\\Guestbook,name,:int:1,_,content,:float:1.5', $rule);
        $this->assertTrue($validate->success());
        $sql = $this->getLastSql('guest_book');
        $sqlResult = "SQL: [188] SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = :guest_book_name AND `guest_book`.`id` <> :guest_book_id AND `guest_book`.`content` = :guest_book_content LIMIT 1 | Params:  3 | Key: Name: [16] :guest_book_name | paramno=0 | name=[16] \":guest_book_name\" | is_param=1 | param_type=2 | Key: Name: [14] :guest_book_id | paramno=1 | name=[14] \":guest_book_id\" | is_param=1 | param_type=1 | Key: Name: [19] :guest_book_content | paramno=2 | name=[19] \":guest_book_content\" | is_param=1 | param_type=2 (SELECT COUNT(*) AS row_count FROM `guest_book` WHERE `guest_book`.`name` = 'foo' AND `guest_book`.`id` <> 1 AND `guest_book`.`content` = 1.5 LIMIT 1)";
        $this->assertSame($sql, $sqlResult);
    }

    public function testValidateValueIsNotString(): void
    {
        $rule = new UniqueRule();
        $this->assertTrue($rule->handle(3, [Guestbook::class], $this->createMock(IValidator::class), 'id'));
        $this->assertTrue($rule->handle(1.5, [Guestbook::class], $this->createMock(IValidator::class), 'id'));
    }

    protected function getDatabaseTable(): array
    {
        return ['guest_book', 'composite_id'];
    }
}

class DemoUnique1
{
}
