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

namespace Tests\Validate;

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
    protected function setUp()
    {
        Validate::initMessages();
    }

    public function testBaseUse()
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

    public function testMake()
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

    public function testError()
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

    public function testData()
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

    public function testAddData()
    {
        $validate = new Validator(
            [
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

    public function testRule()
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

    public function testRuleIf()
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

    public function testAddRule()
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

    public function testAddRuleIf()
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

    public function testMessage()
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

    public function testMessage2()
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

    public function testSubDataWithSubMessage()
    {
        $validate = new Validator(
            [
                'name' => ['sub' => ['sub' => '']],
            ],
            [
                'name.sub.sub' => 'required|'.Validator::CONDITION_MUST,
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

    public function testSubDataWithNotSet()
    {
        $validate = new Validator(
            [
                'name' => ['sub' => ['sub' => null]],
            ],
            [
                'name.sub.sub' => 'required|'.Validator::CONDITION_MUST,
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

    public function testName()
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

    public function testAlias()
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
            [Validator::CONDITION_EXISTS],
            [Validator::CONDITION_MUST],
            [Validator::CONDITION_VALUE],
            [Validator::SKIP_SELF],
            [Validator::SKIP_OTHER],
        ];
    }

    public function testAfter()
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

    public function testExtend()
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

        $validate->extend('custom_rule', function (string $field, $datas, array $parameter): bool {
            if (1 === $datas) {
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

    public function testPlaceholder()
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

        $this->assertInstanceof(Validator::class, $validate->foobar());
        $this->assertInstanceof(Validator::class, $validate->placeholder());
    }

    public function testCall()
    {
        $validate = new Validator();

        $this->assertTrue($validate->minLength('成都', 1));
        $this->assertTrue($validate->minLength('成都', 2));
        $this->assertFalse($validate->minLength('成都', 3));

        $this->assertFalse($validate->alpha('成都'));
        $this->assertTrue($validate->alpha('cd'));
    }

    public function testCallCustom()
    {
        $validate = new Validator();

        $validate->extend('custom_foo_bar', function (string $field, $datas, array $parameter): bool {
            if ('成都' === $datas) {
                return true;
            }

            return false;
        });

        $this->assertTrue($validate->customFooBar('成都'));
        $this->assertFalse($validate->customFooBar('魂之挽歌'));
    }

    public function testCallException()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Method notFoundMethod is not exits.'
        );

        $validate = new Validator();

        $validate->notFoundMethod();
    }

    public function testCheckParameterLengthException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The rule name requires at least 1 arguments.'
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

    public function testCallExtendClassWithContainerNotSetException()
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

    public function testCallExtendClass()
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

    public function testCallExtendClassWithCustomMethod()
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

    public function testCallExtendClassWithRun()
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

    public function testCallExtendClassWithClassNotValidException()
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

    public function testCallExtendNotValidException()
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
            [Validator::CONDITION_EXISTS],
            [Validator::CONDITION_MUST],
            [Validator::CONDITION_VALUE],
            [Validator::SKIP_SELF],
            [Validator::SKIP_OTHER],
        ];
    }

    public function testShouldSkipOther()
    {
        $validate = new Validator(
            [
                'name' => '',
            ],
            [
                'name'     => 'required|alpha',
            ],
            [
                'name'     => '地名',
            ]
        );

        $error = <<<'eot'
{
    "name": [
        "地名 不能为空",
        "地名 只能是字母"
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

        $validate->rule(['name' => 'required|alpha|'.Validator::SKIP_OTHER]);

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

    public function testMustRequired()
    {
        $validate = new Validator(
            [
                'name' => '',
            ],
            [
                'name'     => 'required',
            ],
            [
                'name'     => '地名',
            ]
        );

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '地名'], $validate->getName());

        $validate->rule(['name' => 'required|'.Validator::CONDITION_VALUE]);

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->rule(['name' => 'required|'.Validator::CONDITION_MUST]);

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

    public function testWildcardMessage()
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

    public function testMessageForAllFieldRule()
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

    public function testWildcardRule()
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

    public function testGetFieldRuleButNotSet()
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

    public function testRuleIsEmpty()
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

    public function testHasFieldRuleWithoutParameterRealWithRuleNotSet()
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
        $this->assertTrue($this->invokeTestMethod($validate, 'hasFieldRuleWithoutParameterReal', ['name', ['required']]));
        $this->assertFalse($this->invokeTestMethod($validate, 'hasFieldRuleWithoutParameterReal', ['name', ['foo']]));
        $this->assertFalse($this->invokeTestMethod($validate, 'hasFieldRuleWithoutParameterReal', ['bar', []]));
    }
}

class ExtendClassTest1
{
    public function handle(string $field, $datas, array $parameter): bool
    {
        if (1 === $datas) {
            return true;
        }

        return false;
    }

    public function handle2(string $field, $datas, array $parameter): bool
    {
        if (2 === $datas) {
            return true;
        }

        return false;
    }
}

class ExtendClassTest2
{
    public function handle(string $field, $datas, array $parameter): bool
    {
        if (3 === $datas) {
            return true;
        }

        return false;
    }
}
