<?php

declare(strict_types=1);

namespace Tests\Validate;

use I18nMock;
use Leevel\Di\Container;
use Leevel\Validate\IValidator;
use Leevel\Validate\Validate;
use Leevel\Validate\Validator;
use Tests\TestCase;

/**
 * @api(
 *     title="Validate",
 *     zh-CN:title="验证器",
 *     zh-TW:title="驗證器",
 *     path="validate/README",
 *     zh-CN:description="
 * **构造器函数原型**
 *
 * ``` php
 * public function __construct(array $data = [], array $rules = [], array $names = [], array $messages = []);
 * ```
 *
 *   * $data 验证的数据
 *   * $rules 验证规则
 *   * $names 校验名字隐射
 *   * $messages 校验失败消息
 *
 * 可以通过构造器传递参数，也可以通过 `name`,`message` 等方法传入。
 * ",
 * )
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

    /**
     * @api(
     *     zh-CN:title="验证器基本使用方法",
     *     zh-CN:description="
     * 可以通过 `success` 判断是否通过验证，`error` 返回错误消息。
     * ",
     *     zh-CN:note="",
     * )
     */
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
                    [
                        "required",
                        []
                    ],
                    [
                        "max_length",
                        [
                            10
                        ]
                    ]
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

    /**
     * @api(
     *     zh-CN:title="验证器规则支持数组写法",
     *     zh-CN:description="
     * 可以通过 `success` 判断是否通过验证，`error` 返回错误消息。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testRuleIsArray(): void
    {
        $validate = new Validator(
            [
                'name' => '小牛哥',
            ],
            [
                'name'     => ['required', 'max_length:10'],
            ],
            [
                'name'     => '用户名',
            ]
        );

        $this->assertInstanceof(IValidator::class, $validate);

        $rule = <<<'eot'
            {
                "name": [
                    [
                        "required",
                        []
                    ],
                    [
                        "max_length",
                        [
                            10
                        ]
                    ]
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

    /**
     * @api(
     *     zh-CN:title="验证器规则支持数组写法:每一项都是一个数组(第一个是规则，第一个是参数非数组兼容为数组)",
     *     zh-CN:description="
     * 可以通过 `success` 判断是否通过验证，`error` 返回错误消息。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testRuleIsArray2(): void
    {
        $validate = new Validator(
            [
                'name' => '小牛哥',
            ],
            [
                'name'     => ['required', ['max_length', 10]],
            ],
            [
                'name'     => '用户名',
            ]
        );

        $this->assertInstanceof(IValidator::class, $validate);

        $rule = <<<'eot'
            {
                "name": [
                    [
                        "required",
                        []
                    ],
                    [
                        "max_length",
                        [
                            10
                        ]
                    ]
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

    /**
     * @api(
     *     zh-CN:title="验证器规则支持数组写法:每一项都是一个数组(第一个是规则，第一个是参数数组用法)",
     *     zh-CN:description="
     * 可以通过 `success` 判断是否通过验证，`error` 返回错误消息。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testRuleIsArray3(): void
    {
        $validate = new Validator(
            [
                'name' => '小牛哥',
            ],
            [
                'name'     => ['required', ['max_length', [10]]],
            ],
            [
                'name'     => '用户名',
            ]
        );

        $this->assertInstanceof(IValidator::class, $validate);

        $rule = <<<'eot'
            {
                "name": [
                    [
                        "required",
                        []
                    ],
                    [
                        "max_length",
                        [
                            10
                        ]
                    ]
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

    /**
     * @api(
     *     zh-CN:title="make 创建验证器",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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
                    [
                        "required",
                        []
                    ],
                    [
                        "max_length",
                        [
                            10
                        ]
                    ]
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

    /**
     * @api(
     *     zh-CN:title="验证器校验错误",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="设置校验数据",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="添加校验数据",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="设置校验规则",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="设置校验规则支持条件",
     *     zh-CN:description="第一个闭包条件参数不为空，如果闭包返回 `true` 则添加改验证规则，否则忽略。",
     *     zh-CN:note="",
     * )
     */
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
    }

    public function testRuleIf2(): void
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

    /**
     * @api(
     *     zh-CN:title="添加校验规则",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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
                    [
                        "required",
                        []
                    ],
                    [
                        "min_length",
                        [
                            20
                        ]
                    ]
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

    /**
     * @api(
     *     zh-CN:title="添加校验规则支持条件",
     *     zh-CN:description="第一个闭包条件参数不为空，如果闭包返回 `true` 则添加改验证规则，否则忽略。",
     *     zh-CN:note="",
     * )
     */
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
    }

    public function testAddRuleIf2(): void
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

    /**
     * @api(
     *     zh-CN:title="设置验证消息",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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
    }

    /**
     * @api(
     *     zh-CN:title="添加验证消息",
     *     zh-CN:description="设置规则所有字段的验证消息。",
     *     zh-CN:note="",
     * )
     */
    public function testAddMessage(): void
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
    }

    /**
     * @api(
     *     zh-CN:title="添加指定字段验证规则消息",
     *     zh-CN:description="可以单独为某个字段指定验证消息规则，其它字段验证消息保持不变。",
     *     zh-CN:note="",
     * )
     */
    public function testAddMessageForOneField(): void
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

    /**
     * @api(
     *     zh-CN:title="添加指定字段验证规则消息(圆点分隔)",
     *     zh-CN:description="通过圆点 `.` 分隔开来。",
     *     zh-CN:note="",
     * )
     */
    public function testAddMessageForOneFieldSeparateByDot(): void
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

    /**
     * @api(
     *     zh-CN:title="添加指定多层子字段验证规则消息(圆点分隔)",
     *     zh-CN:description="通过圆点 `.` 分隔开来。",
     *     zh-CN:note="",
     * )
     */
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
    }

    /**
     * @api(
     *     zh-CN:title="添加通配符字段验证规则消息",
     *     zh-CN:description="通过 `*` 来代表通配符。",
     *     zh-CN:note="",
     * )
     */
    public function testWildcardSubDataWithSubMessage(): void
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

    /**
     * @api(
     *     zh-CN:title="设置验证字段隐射",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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
    }

    /**
     * @api(
     *     zh-CN:title="添加验证字段隐射",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testAddName(): void
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

    /**
     * @api(
     *     zh-CN:title="设置验证规则别名",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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
    }

    /**
     * @api(
     *     zh-CN:title="批量设置验证规则别名",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testAliasMany(): void
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
     */
    public function testAliasSkipException(string $skipRule): void
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

    public function aliasSkipExceptionProvider(): array
    {
        return [
            [Validator::OPTIONAL],
            [Validator::MUST],
            [Validator::SKIP_SELF],
            [Validator::SKIP_OTHER],
        ];
    }

    /**
     * @api(
     *     zh-CN:title="验证后回调",
     *     zh-CN:description="无论成功或者失败都会执行回调。",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="自定义扩展验证规则",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="直接调用验证规则",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testCall(): void
    {
        $validate = new Validator();

        $this->assertTrue($validate->minLength('成都', 1));
        $this->assertTrue($validate->minLength('成都', 2));
        $this->assertFalse($validate->minLength('成都', 3));
        $this->assertFalse($validate->alpha('成都'));
        $this->assertTrue($validate->alpha('cd'));
    }

    /**
     * @api(
     *     zh-CN:title="直接调用自定义验证规则",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="自定义扩展验证规则(类)",
     *     zh-CN:description="
     * 自定义扩展规则可以为一个独立的类，例如下面的例子。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Validate\ExtendClassTest1::class)]}
     * ```
     *
     * 默认情况下，此时自定义类的 `handle` 方法将作为验证入口。
     * ",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="自定义扩展验证规则(类)，指定验证方法",
     *     zh-CN:description="
     * 自定义扩展规则可以为一个独立的类，例如下面的例子。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Validate\ExtendClassTest1::class)]}
     * ```
     *
     * 指定方法情况下,通过 `@` 分隔开来，此时自定义类的 `handle2` 方法将作为验证入口。
     * ",
     *     zh-CN:note="",
     * )
     */
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

    public function testCallExtendClassWithCustomMethod2(): void
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
        $this->expectException(\TypeError::class);

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
     */
    public function testSkipRule(string $skipRule): void
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

    public function skipRuleProvider(): array
    {
        return [
            [Validator::OPTIONAL],
            [Validator::MUST],
            [Validator::SKIP_SELF],
            [Validator::SKIP_OTHER],
        ];
    }

    /**
     * @api(
     *     zh-CN:title="验证失败则跳过其它验证规则",
     *     zh-CN:description="
     * 只需要在校验规则中加入 `SKIP_OTHER` 即可。
     * ",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="验证失败则跳过自身其它验证规则",
     *     zh-CN:description="
     * 只需要在校验规则中加入 `SKIP_SELF` 即可，只会跳过当前字段的其他验证规则，而其它字段的验证规则不受影响。
     * ",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="值为 null 会跳过可选验证规则",
     *     zh-CN:description="
     * 如果校验规则中有 `OPTIONAL` ，那么字段值为 `null` 则不会执行验证规则。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testOptional(): void
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
    }

    /**
     * @api(
     *     zh-CN:title="值为 null 默认必须验证",
     *     zh-CN:description="
     * 我们加入 `MUST` 或者默认不指定，那么 `null` 也会执行验证。
     * ",
     *     zh-CN:note="",
     * )
     */
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
                    [
                        "required",
                        []
                    ]
                ],
                "nafoo": [
                    [
                        "required",
                        []
                    ]
                ],
                "nabar": [
                    [
                        "required",
                        []
                    ]
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
                    [
                        "required",
                        []
                    ]
                ],
                "nafoo": [
                    [
                        "required",
                        []
                    ]
                ],
                "nabar": [
                    [
                        "required",
                        []
                    ]
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

    /**
     * @api(
     *     zh-CN:title="通配符验证规则支持",
     *     zh-CN:description="
     * 可以通过 `*` 来表示通配符验证规则。
     * ",
     *     zh-CN:note="",
     * )
     */
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
                    [
                        "required",
                        []
                    ]
                ],
                "nafoo": [
                    [
                        "required",
                        []
                    ]
                ],
                "nabar": [
                    [
                        "required",
                        []
                    ]
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
        $this->assertSame([['required', []]], $this->invokeTestMethod($validate, 'getFieldRule', ['name']));
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
