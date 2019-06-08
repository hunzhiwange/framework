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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
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
    public function testBaseUse(): void
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

    public function testValidateWithExceptId(): void
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

        $this->assertSame(
            '1',
            $connect
                ->table('guest_book')
                ->insert([
                    'name'   => 'foo',
                ]),
        );

        $this->assertTrue($validate->success());
    }

    public function testValidateWithExceptIdAndPrimaryKey(): void
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

        $this->assertSame(
            '1',
            $connect
                ->table('guest_book')
                ->insert([
                    'name'   => 'foo',
                ]),
        );

        $this->assertTrue($validate->success());
    }

    public function testValidateWithExceptIdAndCompositeIdAndIgnore(): void
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

        $connect
            ->table('composite_id')
            ->insert([
                'id1'   => 'foo',
                'id2'   => 'bar',
            ]);

        $this->assertTrue($validate->success());
    }

    public function testValidateWithoutExceptId(): void
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

        $this->assertSame(
            '1',
            $connect
                ->table('guest_book')
                ->insert([
                    'name'   => 'foo',
                ]),
        );

        $this->assertFalse($validate->success());
    }

    public function testCheckParameterLengthException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Missing the first element of parameter.'
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
            '1',
            $connect
                ->table('guest_book')
                ->insert([
                    'name'   => 'value',
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

    public function testValidateWithValidateField(): void
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

        $this->assertSame(
            '1',
            $connect
                ->table('guest_book')
                ->insert([
                    'name'   => 'foo',
                ]),
        );

        $this->assertTrue($validate->success());
    }

    public function testValidateWithValidateMultiField(): void
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

        $this->assertSame(
            '1',
            $connect
                ->table('guest_book')
                ->insert([
                    'name'   => 'foo',
                ]),
        );

        $this->assertTrue($validate->success());
    }

    public function testValidateWithConnect(): void
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

        $this->assertSame(
            '1',
            $connect
                ->table('guest_book')
                ->insert([
                    'name'   => 'foo',
                ]),
        );

        $this->assertTrue($validate->success());
    }

    public function testValidateWithParseAdditional(): void
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

        $this->assertSame(
            '1',
            $connect
                ->table('guest_book')
                ->insert([
                    'name'   => 'foo',
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
                'name'     => UniqueRule::rule(Guestbook::class, null, null, null, 'content', 'hello'),
            ]
        );

        $this->assertTrue($validate->success());

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
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
                'name'     => UniqueRule::rule(Guestbook::class, null, null, null, 'content', 'hello'),
            ]
        );

        $this->assertTrue($validate->success());

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('guest_book')
                ->insert([
                    'name'    => 'foo',
                    'content' => 'world',
                ]),
        );

        $this->assertTrue($validate->success());
    }

    public function testValidateWithParseAdditionalCustomOperate(): void
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

        $this->assertSame(
            '1',
            $connect
                ->table('guest_book')
                ->insert([
                    'name'   => 'foo',
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
                'name'     => UniqueRule::rule(Guestbook::class, null, null, null, 'id'),
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
                'name'     => UniqueRule::rule(Guestbook::class, null, 1),
            ]
        );

        $validate->setContainer(new Container());

        $this->assertTrue($validate->success());
    }

    public function testWithPlaceHolder(): void
    {
        $validate = new Validator(
            [
                'name' => 'foo',
            ],
            [
                'name'     => UniqueRule::rule(Guestbook::class, UniqueRule::PLACEHOLDER, UniqueRule::PLACEHOLDER, UniqueRule::PLACEHOLDER, 'content', 'hello'),
            ]
        );

        $this->assertTrue($validate->success());

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('guest_book')
                ->insert([
                    'name'    => 'foo',
                    'content' => 'hello',
                ]),
        );

        $this->assertFalse($validate->success());
    }

    protected function getDatabaseTable(): array
    {
        return ['guest_book', 'composite_id'];
    }
}

class DemoUnique1
{
}
