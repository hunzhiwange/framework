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
use Leevel\Validate\IValidate;
use Leevel\Validate\Validate;
use Tests\TestCase;

/**
 * validate test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.12
 *
 * @version 1.0
 */
class ValidateTest extends TestCase
{
    public function testBaseUse()
    {
        $validate = new Validate(
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

        $this->assertInstanceof(IValidate::class, $validate);

        $rule = <<<'eot'
array (
  'name' => 
  array (
    0 => 'required',
    1 => 'max_length:10',
  ),
)
eot;

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame([], $validate->error());
        $this->assertSame([], $validate->getMessage());
        $this->assertSame(['name' => '小牛哥'], $validate->getData());

        $this->assertSame(
            $rule,
            $this->varExport(
                $validate->getRule()
            )
        );
    }

    public function testMake()
    {
        $validate = Validate::make(
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
array (
  'name' => 
  array (
    0 => 'required',
    1 => 'max_length:10',
  ),
)
eot;

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame([], $validate->error());
        $this->assertSame([], $validate->getMessage());
        $this->assertSame(['name' => '小牛哥'], $validate->getData());

        $this->assertSame(
            $rule,
            $this->varExport(
                $validate->getRule()
            )
        );
    }

    public function testError()
    {
        $validate = new Validate(
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
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testData()
    {
        $validate = new Validate(
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
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->data(['name' => '12345678901234567890']);

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
    }

    public function testAddData()
    {
        $validate = new Validate(
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
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->addData(['name' => '中国']);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testRule()
    {
        $validate = new Validate(
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
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->rule(['name' => 'required|min_length:20']);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->rule(['name' => 'required|max_length:20']);

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
    }

    public function testRuleIf()
    {
        $validate = new Validate(
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
array (
)
eot;

        $this->assertSame(
            $rule,
            $this->varExport(
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
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testAddRule()
    {
        $validate = new Validate(
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
array (
  'name' => 
  array (
    0 => 'required',
    1 => 'min_length:20',
  ),
)
eot;

        $this->assertSame(
            $rule,
            $this->varExport(
                $validate->getRule()
            )
        );

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testAddRuleIf()
    {
        $validate = new Validate(
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
array (
)
eot;

        $this->assertSame(
            $rule,
            $this->varExport(
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
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testMessage()
    {
        $validate = new Validate(
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
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->message(['min_length' => '{field} not min {rule}']);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 not min 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->addMessage(['min_length' => '{field} foo bar {rule}']);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 foo bar 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->addMessage(['name' => ['min_length' => '{field} hello world {rule}']]);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 hello world 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testMessage2()
    {
        $validate = new Validate(
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
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->addMessage(['min_length' => '{field} not min {rule}']);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 not min 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->addMessage(['name' => ['min_length' => '{field} haha {rule}']]);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 haha 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->addMessage(['name.min_length' => '{field} hehe {rule}']);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 hehe 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testSubDataWithSubMessage()
    {
        $validate = new Validate(
            [
                'name' => ['sub' => ['sub' => '']],
            ],
            [
                'name.sub.sub' => 'required|'.Validate::CONDITION_MUST,
            ],
            [
                'name'     => '歌曲',
            ]
        );

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
array (
  'name.sub.sub' => 
  array (
    0 => 'name.sub.sub 不能为空',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->addMessage(['name.sub.sub' => ['required' => '字段 {field} 不能为空']]);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
array (
  'name.sub.sub' => 
  array (
    0 => '字段 name.sub.sub 不能为空',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->addMessage(['name*' => ['required' => 'sub {field} must have value']]);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
array (
  'name.sub.sub' => 
  array (
    0 => 'sub name.sub.sub must have value',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testSubDataWithNotSet()
    {
        $validate = new Validate(
            [
                'name' => ['sub' => ['sub' => null]],
            ],
            [
                'name.sub.sub' => 'required|'.Validate::CONDITION_MUST,
            ],
            [
                'name'     => '歌曲',
            ]
        );

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
array (
  'name.sub.sub' => 
  array (
    0 => 'name.sub.sub 不能为空',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testName()
    {
        $validate = new Validate(
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
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '用户名'], $validate->getName());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->name(['name' => 'username']);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => 'username 不满足最小长度 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->addName(['name' => 'hello world']);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => 'hello world 不满足最小长度 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testAlias()
    {
        $validate = new Validate(
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
array (
  'name' => 
  array (
    0 => '地名 不满足最小长度 5',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '地名'], $validate->getName());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->alias('min_length', 'minl');

        $validate->rule(['name' => 'required|minl:9']);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '地名 不满足最小长度 9',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->aliasMany(['min_length' => 'min2']);

        $validate->rule(['name' => 'required|min2:11']);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '地名 不满足最小长度 11',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
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
            sprintf('You can not set alias for skip rule %s.', $skipRule)
        );

        $validate = new Validate(
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
            [Validate::CONDITION_EXISTS],
            [Validate::CONDITION_MUST],
            [Validate::CONDITION_VALUE],
            [Validate::SKIP_SELF],
            [Validate::SKIP_OTHER],
        ];
    }

    public function testAfter()
    {
        $validate = new Validate(
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
        $validate = new Validate(
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
        $validate = new Validate(
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

        $this->assertInstanceof(Validate::class, $validate->foobar());
        $this->assertInstanceof(Validate::class, $validate->placeholder());
    }

    public function testCall()
    {
        $validate = new Validate();

        $this->assertTrue($validate->minLength('成都', 1));
        $this->assertTrue($validate->minLength('成都', 2));
        $this->assertFalse($validate->minLength('成都', 3));

        $this->assertFalse($validate->alpha('成都'));
        $this->assertTrue($validate->alpha('cd'));
    }

    public function testCallCustom()
    {
        $validate = new Validate();

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

        $validate = new Validate();

        $validate->notFoundMethod();
    }

    public function testCheckParameterLengthException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The rule name requires at least 1 arguments.'
        );

        $validate = new Validate(
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
            'Container has not set yet.'
        );

        $validate = new Validate(
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

        $validate->extend('tests\\validate\\notfound', 'Tests\\Validate\\NotFound');

        $validate->success();
    }

    public function testCallExtendClass()
    {
        $validate = new Validate(
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
        $validate = new Validate(
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
        $validate = new Validate(
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

        $validate = new Validate(
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

        $validate = new Validate(
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
        $validate = new Validate(
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
            [Validate::CONDITION_EXISTS],
            [Validate::CONDITION_MUST],
            [Validate::CONDITION_VALUE],
            [Validate::SKIP_SELF],
            [Validate::SKIP_OTHER],
        ];
    }

    public function testShouldSkipOther()
    {
        $validate = new Validate(
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
array (
  'name' => 
  array (
    0 => '地名 不能为空',
    1 => '地名 只能是字母',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '地名'], $validate->getName());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->rule(['name' => 'required|alpha|'.Validate::SKIP_OTHER]);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '地名 不能为空',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testMustRequired()
    {
        $validate = new Validate(
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

        $validate->rule(['name' => 'required|'.Validate::CONDITION_VALUE]);

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->rule(['name' => 'required|'.Validate::CONDITION_MUST]);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '地名 不能为空',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->data(['name' => null]);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '地名 不能为空',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testWildcardMessage()
    {
        $validate = new Validate(
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
array (
  'name.required' => 'test {field} required message',
  'nafoo.required' => 'test {field} required message',
  'nabar.required' => 'test {field} required message',
)
eot;

        $this->assertSame(
            $message,
            $this->varExport(
                $validate->getMessage()
            )
        );

        $data = <<<'eot'
array (
  'name' => '',
  'nafoo' => '',
  'nabar' => '',
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $validate->getData()
            )
        );

        $rule = <<<'eot'
array (
  'name' => 
  array (
    0 => 'required',
  ),
  'nafoo' => 
  array (
    0 => 'required',
  ),
  'nabar' => 
  array (
    0 => 'required',
  ),
)
eot;

        $this->assertSame(
            $rule,
            $this->varExport(
                $validate->getRule()
            )
        );

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => 'test 地名 required message',
  ),
  'nafoo' => 
  array (
    0 => 'test foo required message',
  ),
  'nabar' => 
  array (
    0 => 'test bar required message',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testMessageForAllFieldRule()
    {
        $validate = new Validate(
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
array (
  'name.required' => 'test {field} required message',
  'nafoo.required' => 'test {field} required message',
  'nabar.required' => 'test {field} required message',
)
eot;

        $this->assertSame(
            $message,
            $this->varExport(
                $validate->getMessage()
            )
        );

        return;
        $data = <<<'eot'
array (
  'name' => '',
  'nafoo' => '',
  'nabar' => '',
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $validate->getData()
            )
        );

        $rule = <<<'eot'
array (
  'name' => 
  array (
    0 => 'required',
  ),
  'nafoo' => 
  array (
    0 => 'required',
  ),
  'nabar' => 
  array (
    0 => 'required',
  ),
)
eot;

        $this->assertSame(
            $rule,
            $this->varExport(
                $validate->getRule()
            )
        );

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => 'test 地名 required message',
  ),
  'nafoo' => 
  array (
    0 => 'test foo required message',
  ),
  'nabar' => 
  array (
    0 => 'test bar required message',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testWildcardRule()
    {
        $validate = new Validate(
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
array (
  'name' => '',
  'nafoo' => '',
  'nabar' => '',
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $validate->getData()
            )
        );

        $rule = <<<'eot'
array (
  'name' => 
  array (
    0 => 'required',
  ),
  'nafoo' => 
  array (
    0 => 'required',
  ),
  'nabar' => 
  array (
    0 => 'required',
  ),
)
eot;

        $this->assertSame(
            $rule,
            $this->varExport(
                $validate->getRule()
            )
        );

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '地名 不能为空',
  ),
  'nafoo' => 
  array (
    0 => 'foo 不能为空',
  ),
  'nabar' => 
  array (
    0 => 'bar 不能为空',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testGetFieldRuleButNotSet()
    {
        $validate = new Validate(
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
        $validate = new Validate(
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
        $validate = new Validate(
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
    public function run(string $field, $datas, array $parameter): bool
    {
        if (3 === $datas) {
            return true;
        }

        return false;
    }
}
