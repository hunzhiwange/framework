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

namespace Tests\Validate;

use I18nMock;
use Leevel\Di\Container;
use Leevel\Validate\Validate;
use Leevel\Validate\Validator;
use Tests\TestCase;

/**
 * validatorFlow test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.12
 *
 * @version 1.0
 */
class ValidatorFlowTest extends TestCase
{
    protected function setUp(): void
    {
        $container = Container::singletons();
        $container->clear();

        $container->singleton('i18n', function (): I18nMock {
            return new I18nMock();
        });

        Validate::initMessages();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testData(): void
    {
        $validate = $this->makeBaseValidate();

        $condition = false;

        $validate
            ->ifs($condition)
            ->data(['name' => 'foo'])
            ->elses()
            ->data(['name' => 'bar'])
            ->endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => 'bar'], $validate->getData());
    }

    public function testData2(): void
    {
        $validate = $this->makeBaseValidate();

        $condition = true;

        $validate
            ->ifs($condition)
            ->data(['name' => 'foo'])
            ->elses()
            ->data(['name' => 'bar'])
            ->endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => 'foo'], $validate->getData());
    }

    public function testAddData(): void
    {
        $validate = $this->makeBaseValidate();

        $condition = false;

        $validate
            ->ifs($condition)
            ->addData(['name' => 'foo'])
            ->elses()
            ->addData(['name' => 'bar'])
            ->endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => 'bar'], $validate->getData());
    }

    public function testAddData2(): void
    {
        $validate = $this->makeBaseValidate();

        $condition = true;

        $validate
            ->ifs($condition)
            ->addData(['name' => 'foo'])
            ->elses()
            ->addData(['name' => 'bar'])
            ->endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => 'foo'], $validate->getData());
    }

    public function testRule(): void
    {
        $validate = $this->makeBaseValidate();

        $condition = false;

        $validate
            ->ifs($condition)
            ->rule(['name' => 'required|max_length:9'])
            ->elses()
            ->rule(['name' => 'required|max_length:2'])
            ->endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testRule2(): void
    {
        $validate = $this->makeBaseValidate();

        $condition = true;

        $validate
            ->ifs($condition)
            ->rule(['name' => 'required|max_length:9'])
            ->elses()
            ->rule(['name' => 'required|max_length:2'])
            ->endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testRuleIf(): void
    {
        $validate = $this->makeBaseValidate();

        $condition = false;

        $validate
            ->ifs($condition)
            ->rule(['name' => 'required|max_length:9'], function (array $data) {
                $this->assertSame(['name' => '小牛神'], $data);

                return true;
            })
            ->elses()
            ->rule(['name' => 'required|max_length:2'], function (array $data) {
                $this->assertSame(['name' => '小牛神'], $data);

                return true;
            })
            ->endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testRuleIf2(): void
    {
        $validate = $this->makeBaseValidate();

        $condition = true;

        $validate
            ->ifs($condition)
            ->rule(['name' => 'required|max_length:9'], function (array $data) {
                $this->assertSame(['name' => '小牛神'], $data);

                return true;
            })
            ->elses()
            ->rule(['name' => 'required|max_length:2'], function (array $data) {
                $this->assertSame(['name' => '小牛神'], $data);

                return true;
            })
            ->endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testAddRule(): void
    {
        $validate = $this->makeBaseValidate();

        $condition = false;

        $validate
            ->ifs($condition)
            ->addRule(['name' => 'required|max_length:9'])
            ->elses()
            ->addRule(['name' => 'required|max_length:2'])
            ->endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testAddRule2(): void
    {
        $validate = $this->makeBaseValidate();

        $condition = true;

        $validate
            ->ifs($condition)
            ->addRule(['name' => 'required|max_length:9'])
            ->elses()
            ->addRule(['name' => 'required|max_length:2'])
            ->endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testAddRuleIf(): void
    {
        $validate = $this->makeBaseValidate();

        $condition = false;

        $validate
            ->ifs($condition)
            ->addRule(['name' => 'required|max_length:9'], function (array $data) {
                $this->assertSame(['name' => '小牛神'], $data);

                return true;
            })
            ->elses()
            ->addRule(['name' => 'required|max_length:2'], function (array $data) {
                $this->assertSame(['name' => '小牛神'], $data);

                return true;
            })
            ->endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testAddRuleIf2(): void
    {
        $validate = $this->makeBaseValidate();

        $condition = true;

        $validate
            ->ifs($condition)
            ->addRule(['name' => 'required|max_length:9'], function (array $data) {
                $this->assertSame(['name' => '小牛神'], $data);

                return true;
            })
            ->elses()
            ->addRule(['name' => 'required|max_length:2'], function (array $data) {
                $this->assertSame(['name' => '小牛神'], $data);

                return true;
            })
            ->endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testMessage(): void
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = false;

        $validate
            ->ifs($condition)
            ->message(['min_length' => '{field} foo min {rule}'])
            ->elses()
            ->message(['min_length' => '{field} bar min {rule}'])
            ->endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
            {
                "name": [
                    "用户名 bar min 9"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function testMessage2(): void
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = true;

        $validate
            ->ifs($condition)
            ->message(['min_length' => '{field} foo min {rule}'])
            ->elses()
            ->message(['min_length' => '{field} bar min {rule}'])
            ->endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
            {
                "name": [
                    "用户名 foo min 9"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function testAddMessage(): void
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = false;

        $validate
            ->

        ifs($condition)
            ->addMessage(['min_length' => '{field} foo min {rule}'])
            ->elses()
            ->addMessage(['min_length' => '{field} bar min {rule}'])
            ->endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
            {
                "name": [
                    "用户名 bar min 9"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function testAddMessage2(): void
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = true;

        $validate
            ->ifs($condition)
            ->addMessage(['min_length' => '{field} foo min {rule}'])
            ->elses()
            ->addMessage(['min_length' => '{field} bar min {rule}'])
            ->endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
            {
                "name": [
                    "用户名 foo min 9"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function testMessageWithField(): void
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = false;

        $validate
            ->ifs($condition)
            ->addMessage(['name' => ['min_length' => '{field} hello foo {rule}']])
            ->elses()
            ->addMessage(['name' => ['min_length' => '{field} hello bar {rule}']])
            ->endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
            {
                "name": [
                    "用户名 hello bar 9"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function testMessageWithField2(): void
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = true;

        $validate
            ->ifs($condition)
            ->addMessage(['name' => ['min_length' => '{field} hello foo {rule}']])
            ->elses()
            ->addMessage(['name' => ['min_length' => '{field} hello bar {rule}']])
            ->endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
            {
                "name": [
                    "用户名 hello foo 9"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function testName(): void
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = false;

        $validate
            ->ifs($condition)
            ->name(['name' => 'foo'])
            ->elses()
            ->name(['name' => 'bar'])
            ->endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
            {
                "name": [
                    "bar 不满足最小长度 9"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function testName2(): void
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = true;

        $validate
            ->ifs($condition)
            ->name(['name' => 'foo'])
            ->elses()
            ->name(['name' => 'bar'])
            ->endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
            {
                "name": [
                    "foo 不满足最小长度 9"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function testAddName(): void
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = false;

        $validate
            ->ifs($condition)
            ->addName(['name' => 'foo'])
            ->elses()
            ->addName(['name' => 'bar'])
            ->endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
            {
                "name": [
                    "bar 不满足最小长度 9"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function testAddName2(): void
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = true;

        $validate
            ->ifs($condition)
            ->addName(['name' => 'foo'])
            ->elses()
            ->addName(['name' => 'bar'])
            ->endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
            {
                "name": [
                    "foo 不满足最小长度 9"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    protected function makeBaseValidate(): Validator
    {
        $validate = new Validator(
            [
                'name' => '小牛神',
            ],
            [
                'name'     => 'required|max_length:10',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame([], $validate->error());
        $this->assertSame([], $validate->getMessage());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        return $validate;
    }
}
