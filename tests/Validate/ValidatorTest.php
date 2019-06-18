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
use Leevel\Validate\IValidator;
use Leevel\Validate\Validate;
use Leevel\Validate\Validator;
use Tests\TestCase;

/**
 * validator test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.12
 *
 * @version 1.0
 */
class ValidatorTest extends TestCase
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

    public function testBaseUse(): void
    {
        $validate = new Validator(
            [
                'name' => '小牛哥',
            ],
            [
                'name'     => 'required|max_length:10',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $this->assertInstanceof(IValidator::class, $validate);

        $rule = <<<'eot'
            {
                "name": [
                    "required",
                    "max_length:10"
                ]
            }
            eot;

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame([], $validate->error());
        $this->assertSame([], $validate->getMessage());
        $this->assertSame(['name' => '小牛哥'], $validate->getData());

        $this->assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );
    }

    public function testMake(): void
    {
        $validate = Validator::make(
            [
                'name' => '小牛哥',
            ],
            [
                'name'     => 'required|max_length:10',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $rule = <<<'eot'
            {
                "name": [
                    "required",
                    "max_length:10"
                ]
            }
            eot;

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame([], $validate->error());
        $this->assertSame([], $validate->getMessage());
        $this->assertSame(['name' => '小牛哥'], $validate->getData());

        $this->assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );
    }

    public function testError(): void
    {
        $validate = new Validator(
            [
                'name' => '小牛哥',
            ],
            [
                'name'     => 'required|min_length:20',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function testData(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
                'name'     => 'required|min_length:20',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->data(['name' => '12345678901234567890']);

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
    }

    public function testAddData(): void
    {
        $validate = new Validator(
            [
            ],
            [
                'name'     => 'required|min_length:20|'.IValidator::OPTIONAL,
            ],
            [
                'name'     => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->addData(['name' => '中国']);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function testRule(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
            ],
            [
                'name'     => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->rule(['name' => 'required|min_length:20']);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->rule(['name' => 'required|max_length:20']);

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
    }

    public function testRuleIf(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
            ],
            [
                'name'     => '用户名',
            ]
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->rule(['name' => 'required|min_length:20'], function (array $data) {
            $this->assertSame(['name' => '中国'], $data);

            return false;
        });

        $rule = <<<'eot'
            []
            eot;

        $this->assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->rule(['name' => 'required|min_length:20'], function (array $data) {
            $this->assertSame(['name' => '中国'], $data);

            return true;
        });

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
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

    public function testAddRule(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
            ],
            [
                'name'     => '用户名',
            ]
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->addRule(['name' => 'required|min_length:20']);

        $rule = <<<'eot'
            {
                "name": [
                    "required",
                    "min_length:20"
                ]
            }
            eot;

        $this->assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
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

    public function testAddRuleIf(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
            ],
            [
                'name'     => '用户名',
            ]
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->addRule(['name' => 'required|min_length:20'], function (array $data) {
            $this->assertSame(['name' => '中国'], $data);

            return false;
        });

        $rule = <<<'eot'
            []
            eot;

        $this->assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->addRule(['name' => 'required|min_length:20'], function (array $data) {
            $this->assertSame(['name' => '中国'], $data);

            return true;
        });

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
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

    public function testMessage(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
                'name'     => 'required|min_length:20',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->message(['min_length' => '{field} not min {rule}']);

        $error = <<<'eot'
            {
                "name": [
                    "用户名 not min 20"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->addMessage(['min_length' => '{field} foo bar {rule}']);

        $error = <<<'eot'
            {
                "name": [
                    "用户名 foo bar 20"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->addMessage(['name' => ['min_length' => '{field} hello world {rule}']]);

        $error = <<<'eot'
            {
                "name": [
                    "用户名 hello world 20"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function testMessage2(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
                'name'     => 'required|min_length:20',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->addMessage(['min_length' => '{field} not min {rule}']);

        $error = <<<'eot'
            {
                "name": [
                    "用户名 not min 20"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->addMessage(['name' => ['min_length' => '{field} haha {rule}']]);

        $error = <<<'eot'
            {
                "name": [
                    "用户名 haha 20"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->addMessage(['name.min_length' => '{field} hehe {rule}']);

        $error = <<<'eot'
            {
                "name": [
                    "用户名 hehe 20"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function testSubDataWithSubMessage(): void
    {
        $validate = new Validator(
            [
                'name' => ['sub' => ['sub' => '']],
            ],
            [
                'name.sub.sub' => 'required|'.Validator::MUST,
            ],
            [
                'name'     => '歌曲',
            ]
        );

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name.sub.sub": [
                    "name.sub.sub 不能为空"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->addMessage(['name.sub.sub' => ['required' => '字段 {field} 不能为空']]);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name.sub.sub": [
                    "字段 name.sub.sub 不能为空"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->addMessage(['name*' => ['required' => 'sub {field} must have value']]);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name.sub.sub": [
                    "sub name.sub.sub must have value"
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

    public function testSubDataWithNotSet(): void
    {
        $validate = new Validator(
            [
                'name' => ['sub' => ['sub' => null]],
            ],
            [
                'name.sub.sub' => 'required|'.Validator::MUST,
            ],
            [
                'name'     => '歌曲',
            ]
        );

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name.sub.sub": [
                    "name.sub.sub 不能为空"
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
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
                'name'     => 'required|min_length:20',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '用户名'], $validate->getName());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->name(['name' => 'username']);

        $error = <<<'eot'
            {
                "name": [
                    "username 不满足最小长度 20"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->addName(['name' => 'hello world']);

        $error = <<<'eot'
            {
                "name": [
                    "hello world 不满足最小长度 20"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function testAlias(): void
    {
        $validate = new Validator(
            [
                'name' => '成都',
            ],
            [
                'name'     => 'required|min_length:5',
            ],
            [
                'name'     => '地名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "地名 不满足最小长度 5"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '地名'], $validate->getName());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->alias('min_length', 'minl');

        $validate->rule(['name' => 'required|minl:9']);

        $error = <<<'eot'
            {
                "name": [
                    "地名 不满足最小长度 9"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->aliasMany(['min_length' => 'min2']);

        $validate->rule(['name' => 'required|min2:11']);

        $error = <<<'eot'
            {
                "name": [
                    "地名 不满足最小长度 11"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    /**
     * @dataProvider aliasSkipExceptionProvider
     *
     * @param string $skipRule
     */
    public function testAliasSkipException(string $skipRule)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('You cannot set alias for skip rule %s.', $skipRule)
        );

        $validate = new Validator(
            [
                'name' => '成都',
            ],
            [
                'name'     => 'required|min_length:5',
            ],
            [
                'name'     => '地名',
            ]
        );

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $validate->alias($skipRule, 'custom_bar');
    }

    public function aliasSkipExceptionProvider()
    {
        return [
            [Validator::OPTIONAL],
            [Validator::MUST],
            [Validator::SKIP_SELF],
            [Validator::SKIP_OTHER],
        ];
    }

    public function testAfter(): void
    {
        $validate = new Validator(
            [
                'name' => '成都',
            ],
            [
                'name'     => 'required|max_length:10',
            ],
            [
                'name'     => '地名',
            ]
        );

        $validate->after(function ($v) {
            $this->assertSame(['name' => '地名'], $v->getName());
        });

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
    }

    public function testExtend(): void
    {
        $validate = new Validator(
            [
                'name' => 1,
            ],
            [
                'name'     => 'required|custom_rule:10',
            ],
            [
                'name'     => '地名',
            ]
        );

        $validate->extend('custom_rule', function ($value, array $param, IValidator $validator, string $field): bool {
            if (1 === $value) {
                return true;
            }

            return false;
        });

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->data(['name' => 0]);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
    }

    public function testPlaceholder(): void
    {
        $validate = new Validator(
            [
                'name' => 1,
            ],
            [
                'name'     => 'required|custom_rule:10',
            ],
            [
                'name'     => '地名',
            ]
        );

        $this->assertInstanceof(Validator::class, $validate->_());
    }

    public function testCall(): void
    {
        $validate = new Validator();

        $this->assertTrue($validate->minLength('成都', 1));
        $this->assertTrue($validate->minLength('成都', 2));
        $this->assertFalse($validate->minLength('成都', 3));

        $this->assertFalse($validate->alpha('成都'));
        $this->assertTrue($validate->alpha('cd'));
    }

    public function testCallCustom(): void
    {
        $validate = new Validator();

        $validate->extend('custom_foo_bar', function (string $field, $value, array $param): bool {
            if ('成都' === $value) {
                return true;
            }

            return false;
        });

        $this->assertTrue($validate->customFooBar('成都'));
        $this->assertFalse($validate->customFooBar('魂之挽歌'));
    }

    public function testCallException(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Method notFoundMethod is not exits.'
        );

        $validate = new Validator();

        $validate->notFoundMethod();
    }

    public function testCheckParamLengthException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Missing the first element of param.'
        );

        $validate = new Validator(
            [
                'name' => 1,
            ],
            [
                'name'     => 'required|min_length',
            ],
            [
                'name'     => '地名',
            ]
        );

        $validate->success();
    }

    public function testCallExtendClassWithContainerNotSetException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Container was not set.'
        );

        $validate = new Validator(
            [
                'name' => 1,
            ],
            [
                'name'     => 'Tests\\Validate\\NotFound',
            ],
            [
                'name'     => '地名',
            ]
        );

        $validate->extend('tests\\validate\\not_found', 'Tests\\Validate\\NotFound');

        $validate->success();
    }

    public function testCallExtendClass(): void
    {
        $validate = new Validator(
            [
                'name' => 1,
            ],
            [
                'name'     => 'custom_foobar',
            ],
            [
                'name'     => '地名',
            ]
        );

        $container = new Container();

        $validate->setContainer($container);

        $validate->extend('custom_foobar', ExtendClassTest1::class);

        $this->assertTrue($validate->success());

        $validate->data(['name' => 'foo']);

        $this->assertFalse($validate->success());
    }

    public function testCallExtendClassWithCustomMethod(): void
    {
        $validate = new Validator(
            [
                'name' => 2,
            ],
            [
                'name'     => 'custom_foobar',
            ],
            [
                'name'     => '地名',
            ]
        );

        $container = new Container();

        $validate->setContainer($container);

        $validate->extend('custom_foobar', ExtendClassTest1::class.'@handle2');

        $this->assertTrue($validate->success());

        $validate->data(['name' => 'foo']);

        $this->assertFalse($validate->success());
    }

    public function testCallExtendClassWithRun(): void
    {
        $validate = new Validator(
            [
                'name' => 3,
            ],
            [
                'name'     => 'custom_foobar',
            ],
            [
                'name'     => '地名',
            ]
        );

        $container = new Container();

        $validate->setContainer($container);

        $validate->extend('custom_foobar', ExtendClassTest2::class);

        $this->assertTrue($validate->success());

        $validate->data(['name' => 'foo']);

        $this->assertFalse($validate->success());
    }

    public function testCallExtendClassWithClassNotValidException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Extend class Tests\\Validate\\NotFound is not valid.'
        );

        $validate = new Validator(
            [
                'name' => 1,
            ],
            [
                'name'     => 'custom_foobar',
            ],
            [
                'name'     => '地名',
            ]
        );

        $container = new Container();

        $validate->setContainer($container);

        $validate->extend('custom_foobar', 'Tests\\Validate\\NotFound');

        $validate->success();
    }

    public function testCallExtendNotValidException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Extend in rule custom_foobar is not valid.'
        );

        $validate = new Validator(
            [
                'name' => 1,
            ],
            [
                'name'     => 'custom_foobar',
            ],
            [
                'name'     => '地名',
            ]
        );

        $container = new Container();

        $validate->setContainer($container);

        $validate->extend('custom_foobar', ['foo' => 'bar']);

        $validate->success();
    }

    /**
     * @dataProvider skipRuleProvider
     *
     * @param string $skipRule
     */
    public function testSkipRule(string $skipRule)
    {
        $validate = new Validator(
            [
                'name' => 2,
            ],
            [
                'name'     => $skipRule,
            ],
            [
                'name'     => '地名',
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function skipRuleProvider()
    {
        return [
            [Validator::OPTIONAL],
            [Validator::MUST],
            [Validator::SKIP_SELF],
            [Validator::SKIP_OTHER],
        ];
    }

    public function testShouldSkipOther(): void
    {
        $validate = new Validator(
            [
                'name'  => '',
                'value' => '',
            ],
            [
                'name'     => 'required|alpha',
                'value'    => 'required',
            ],
            [
                'name'      => '地名',
                'value'     => '值',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "地名 不能为空",
                    "地名 只能是字母"
                ],
                "value": [
                    "值 不能为空"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '地名', 'value' => '值'], $validate->getName());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->addRule(['name' => 'required|alpha|'.Validator::SKIP_OTHER]);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name": [
                    "地名 不能为空"
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

    public function testShouldSkipSelf(): void
    {
        $validate = new Validator(
            [
                'name'  => '',
                'value' => '',
            ],
            [
                'name'     => 'required|alpha',
                'value'    => 'required',
            ],
            [
                'name'      => '地名',
                'value'     => '值',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "地名 不能为空",
                    "地名 只能是字母"
                ],
                "value": [
                    "值 不能为空"
                ]
            }
            eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '地名', 'value' => '值'], $validate->getName());

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->addRule(['name' => 'required|alpha|'.Validator::SKIP_SELF]);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name": [
                    "地名 不能为空"
                ],
                "value": [
                    "值 不能为空"
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

    public function testMustRequired(): void
    {
        $validate = new Validator(
            [
                'name' => null,
            ],
            [
                'name'     => 'required|'.Validator::OPTIONAL,
            ],
            [
                'name'     => '地名',
            ]
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => '地名'], $validate->getName());

        $validate->rule(['name' => 'required']);
        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name": [
                    "地名 不能为空"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->data(['name' => null]);
        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name": [
                    "地名 不能为空"
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

    public function testWildcardMessage(): void
    {
        $validate = new Validator(
            [
                'name'  => '',
                'nafoo' => '',
                'nabar' => '',
            ],
            [
                'name'      => 'required',
                'nafoo'     => 'required',
                'nabar'     => 'required',
            ],
            [
                'name'      => '地名',
                'nafoo'     => 'foo',
                'nabar'     => 'bar',
            ]
        );

        $validate->addMessage(['na*' => ['required' => 'test {field} required message']]);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '地名', 'nafoo' => 'foo', 'nabar' => 'bar'], $validate->getName());

        $message = <<<'eot'
            {
                "name.required": "test {field} required message",
                "nafoo.required": "test {field} required message",
                "nabar.required": "test {field} required message"
            }
            eot;

        $this->assertSame(
            $message,
            $this->varJson(
                $validate->getMessage()
            )
        );

        $data = <<<'eot'
            {
                "name": "",
                "nafoo": "",
                "nabar": ""
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $validate->getData(),
                1
            )
        );

        $rule = <<<'eot'
            {
                "name": [
                    "required"
                ],
                "nafoo": [
                    "required"
                ],
                "nabar": [
                    "required"
                ]
            }
            eot;

        $this->assertSame(
            $rule,
            $this->varJson(
                $validate->getRule(),
                2
            )
        );

        $error = <<<'eot'
            {
                "name": [
                    "test 地名 required message"
                ],
                "nafoo": [
                    "test foo required message"
                ],
                "nabar": [
                    "test bar required message"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error(),
                3
            )
        );
    }

    public function testMessageForAllFieldRule(): void
    {
        $validate = new Validator(
            [
                'name'  => '',
                'nafoo' => '',
                'nabar' => '',
            ],
            [
                'name'      => 'required',
                'nafoo'     => 'required',
                'nabar'     => 'required',
            ],
            [
                'name'      => '地名',
                'nafoo'     => 'foo',
                'nabar'     => 'bar',
            ]
        );

        $validate->addMessage(['na*' => 'test {field} required message']);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '地名', 'nafoo' => 'foo', 'nabar' => 'bar'], $validate->getName());

        $message = <<<'eot'
            {
                "name.required": "test {field} required message",
                "nafoo.required": "test {field} required message",
                "nabar.required": "test {field} required message"
            }
            eot;

        $this->assertSame(
            $message,
            $this->varJson(
                $validate->getMessage()
            )
        );

        $data = <<<'eot'
            {
                "name": "",
                "nafoo": "",
                "nabar": ""
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $validate->getData(),
                1
            )
        );

        $rule = <<<'eot'
            {
                "name": [
                    "required"
                ],
                "nafoo": [
                    "required"
                ],
                "nabar": [
                    "required"
                ]
            }
            eot;

        $this->assertSame(
            $rule,
            $this->varJson(
                $validate->getRule(),
                2
            )
        );

        $error = <<<'eot'
            {
                "name": [
                    "test 地名 required message"
                ],
                "nafoo": [
                    "test foo required message"
                ],
                "nabar": [
                    "test bar required message"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error(),
                3
            )
        );
    }

    public function testWildcardRule(): void
    {
        $validate = new Validator(
            [
                'name'  => '',
                'nafoo' => '',
                'nabar' => '',
            ],
            [
            ],
            [
                'name'      => '地名',
                'nafoo'     => 'foo',
                'nabar'     => 'bar',
            ]
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->rule(['na*' => 'required']);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '地名', 'nafoo' => 'foo', 'nabar' => 'bar'], $validate->getName());

        $data = <<<'eot'
            {
                "name": "",
                "nafoo": "",
                "nabar": ""
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $validate->getData()
            )
        );

        $rule = <<<'eot'
            {
                "name": [
                    "required"
                ],
                "nafoo": [
                    "required"
                ],
                "nabar": [
                    "required"
                ]
            }
            eot;

        $this->assertSame(
            $rule,
            $this->varJson(
                $validate->getRule(),
                1
            )
        );

        $error = <<<'eot'
            {
                "name": [
                    "地名 不能为空"
                ],
                "nafoo": [
                    "foo 不能为空"
                ],
                "nabar": [
                    "bar 不能为空"
                ]
            }
            eot;

        $this->assertSame(
            $error,
            $this->varJson(
                $validate->error(),
                2
            )
        );
    }

    public function testGetFieldRuleButNotSet(): void
    {
        $validate = new Validator(
            [
                'name' => '大地',
            ],
            [
                'name' => 'required',
            ],
            [
                'name'     => '歌曲',
            ]
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['required'], $this->invokeTestMethod($validate, 'getFieldRule', ['name']));
        $this->assertSame([], $this->invokeTestMethod($validate, 'getFieldRule', ['foo']));
    }

    public function testRuleIsEmpty(): void
    {
        $validate = new Validator(
            [
                'name' => 'hello',
            ],
            [
                'name' => ':foo|',
            ],
            [
                'name'     => '歌曲',
            ]
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
    }

    public function testHasFieldRuleWithoutParamRealWithRuleNotSet(): void
    {
        $validate = new Validator(
            [
                'name' => 'hello',
            ],
            [
                'name' => 'required',
            ],
            [
                'name'     => '歌曲',
            ]
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertTrue($this->invokeTestMethod($validate, 'hasFieldRuleWithParam', ['name', 'required']));
        $this->assertFalse($this->invokeTestMethod($validate, 'hasFieldRuleWithParam', ['name', 'foo']));
        $this->assertFalse($this->invokeTestMethod($validate, 'hasFieldRuleWithParam', ['bar', '']));
    }
}

class ExtendClassTest1
{
    public function handle($value, array $param, IValidator $validator, string $field): bool
    {
        if (1 === $value) {
            return true;
        }

        return false;
    }

    public function handle2($value, array $param, IValidator $validator, string $field): bool
    {
        if (2 === $value) {
            return true;
        }

        return false;
    }
}

class ExtendClassTest2
{
    public function handle($value, array $param, IValidator $validator, string $field): bool
    {
        if (3 === $value) {
            return true;
        }

        return false;
    }
}
