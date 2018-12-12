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

namespace Tests\Validate\Validator;

use Leevel\Di\Container;
use Leevel\Validate\UniqueRule;
use Leevel\Validate\Validator;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\CompositeId;
use Tests\Database\Ddd\Entity\Guestbook;

/**
 * unique test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.12.04
 *
 * @version 1.0
 */
class UniqueTest extends TestCase
{
    public function testBaseUse()
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, null, 1),
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function testValidateWithExceptId()
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, null, 1),
            ]
        );

        $this->assertTrue($validate->success());

        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('guest_book')->
        insert([
            'name'   => 'foo',
        ]));

        $this->assertTrue($validate->success());
    }

    public function testValidateWithExceptIdAndPrimaryKey()
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, null, 1, 'id'),
            ]
        );

        $this->assertTrue($validate->success());

        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('guest_book')->
        insert([
            'name'   => 'foo',
        ]));

        $this->assertTrue($validate->success());
    }

    public function testValidateWithExceptIdAndCompositeIdAndIgnore()
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(CompositeId::class, null, 1),
            ]
        );

        $this->assertTrue($validate->success());

        $connect = $this->createDatabaseConnect();

        $connect->
        table('composite_id')->
        insert([
            'id1'   => 'foo',
            'id2'   => 'bar',
        ]);

        $this->assertTrue($validate->success());
    }

    public function testValidateWithoutExceptId()
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, null),
            ]
        );

        $this->assertTrue($validate->success());

        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('guest_book')->
        insert([
            'name'   => 'foo',
        ]));

        $this->assertFalse($validate->success());
    }

    public function testCheckParameterLengthException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The rule name requires at least 1 arguments.'
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

    public function testAdditionalConditionsMustBeStringException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unique additional conditions must be string.'
        );

        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, null, 1, null, ['arr']),
            ]
        );
    }

    public function testEntityNotFoundException()
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

    public function testValidateArgsNotObjectAndNotStringWillReturnFalse()
    {
        $rule = new UniqueRule();

        $this->assertFalse($rule->validate('field', 'value', [['arr']]));
    }

    public function testValidateArgsIsEntity()
    {
        $rule = new UniqueRule();

        $this->assertTrue($rule->validate('name', 'value', [new Guestbook()]));

        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('guest_book')->
        insert([
            'name'   => 'value',
        ]));

        $this->assertFalse($rule->validate('name', 'value', [new Guestbook()]));
    }

    public function testValidateArgsIsObjectButNotIsEntity()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Validate entity `Tests\\Validate\\Validator\\DemoUnique1` must be an entity.'
        );

        $rule = new UniqueRule();

        $rule->validate('name', 'value', [new DemoUnique1()]);
    }

    public function testValidateArgsIsStringButNotIsEntity()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Validate entity `Tests\\Validate\\Validator\\DemoUnique1` must be an entity.'
        );

        $rule = new UniqueRule();

        $rule->validate('name', 'value', ['Tests\\Validate\\Validator\\DemoUnique1']);
    }

    public function testValidateWithValidateField()
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, 'name', 1),
            ]
        );

        $this->assertTrue($validate->success());

        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('guest_book')->
        insert([
            'name'   => 'foo',
        ]));

        $this->assertTrue($validate->success());
    }

    public function testValidateWithValidateMultiField()
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, 'name:content', 1),
            ]
        );

        $this->assertTrue($validate->success());

        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('guest_book')->
        insert([
            'name'   => 'foo',
        ]));

        $this->assertTrue($validate->success());
    }

    public function testValidateWithConnect()
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule('fooconnect:'.Guestbook::class, 'name:content', 1),
            ]
        );

        $this->assertTrue($validate->success());

        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('guest_book')->
        insert([
            'name'   => 'foo',
        ]));

        $this->assertTrue($validate->success());
    }

    public function testValidateWithParseAdditional()
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, null, null, null, 'id', '1'),
            ]
        );

        $this->assertTrue($validate->success());

        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('guest_book')->
        insert([
            'name'   => 'foo',
        ]));

        $this->assertFalse($validate->success());
    }

    public function testValidateWithParseAdditional2()
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, null, null, null, 'content', 'hello'),
            ]
        );

        $this->assertTrue($validate->success());

        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('guest_book')->
        insert([
            'name'    => 'foo',
            'content' => 'hello',
        ]));

        $this->assertFalse($validate->success());
    }

    public function testValidateWithParseAdditional3()
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, null, null, null, 'content', 'hello'),
            ]
        );

        $this->assertTrue($validate->success());

        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('guest_book')->
        insert([
            'name'    => 'foo',
            'content' => 'world',
        ]));

        $this->assertTrue($validate->success());
    }

    public function testValidateWithParseAdditionalCustomOperate()
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, null, null, null, 'id:>', '1'),
            ]
        );

        $this->assertTrue($validate->success());

        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('guest_book')->
        insert([
            'name'   => 'foo',
        ]));

        $this->assertTrue($validate->success());
    }

    public function testValidateWithParseAdditionalMustBePairedException()
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
                'name'     => UniqueRule::rule(Guestbook::class, null, null, null, 'id'),
            ]
        );

        $validate->success();
    }

    public function testWithContainer()
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, null, 1),
            ]
        );

        $validate->setContainer(new Container());

        $this->assertTrue($validate->success());
    }

    protected function getDatabaseTable(): array
    {
        return ['guest_book', 'composite_id'];
    }
}

class DemoUnique1
{
}
