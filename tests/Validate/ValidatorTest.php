<?php

declare(strict_types=1);

namespace Tests\Validate;

use Leevel\Di\Container;
use Leevel\Kernel\Utils\Api;
use Leevel\Validate\IValidator;
use Leevel\Validate\Validate;
use Leevel\Validate\Validator;
use Leevel\Validate\ValidatorException;
use Tests\TestCase;

#[Api([
    'title' => 'Validate',
    'zh-CN:title' => '验证器',
    'zh-TW:title' => '驗證器',
    'path' => 'validate/index',
    'zh-CN:description' => <<<'EOT'
**构造器函数原型**

``` php
public function __construct(array $data = [], array $rules = [], array $names = [], array $messages = []);
```

  * $data 验证的数据
  * $rules 验证规则
  * $names 校验名字隐射
  * $messages 校验失败消息

可以通过构造器传递参数，也可以通过 `name`,`message` 等方法传入。
EOT,
])]
final class ValidatorTest extends TestCase
{
    protected function setUp(): void
    {
        $container = Container::singletons();
        $container->clear();

        $container->singleton('i18n', function (): \I18nMock {
            return new \I18nMock();
        });

        Validate::initMessages();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    #[Api([
        'zh-CN:title' => '验证器基本使用方法',
        'zh-CN:description' => <<<'EOT'
可以通过 `success` 判断是否通过验证，`error` 返回错误消息。
EOT,
    ])]
    public function testBaseUse(): void
    {
        $validate = new Validator(
            [
                'name' => '小牛哥',
            ],
            [
                'name' => 'required|max_length:10',
            ],
            [
                'name' => '用户名',
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
                            "10"
                        ]
                    ]
                ]
            }
            eot;

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame([], $validate->error());
        static::assertSame([], $validate->getMessage());
        static::assertSame(['name' => '小牛哥'], $validate->getData());

        static::assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '验证器规则支持数组写法',
        'zh-CN:description' => <<<'EOT'
可以通过 `success` 判断是否通过验证，`error` 返回错误消息。
EOT,
    ])]
    public function testRuleIsArray(): void
    {
        $validate = new Validator(
            [
                'name' => '小牛哥',
            ],
            [
                'name' => ['required', 'max_length:10'],
            ],
            [
                'name' => '用户名',
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
                            "10"
                        ]
                    ]
                ]
            }
            eot;

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame([], $validate->error());
        static::assertSame([], $validate->getMessage());
        static::assertSame(['name' => '小牛哥'], $validate->getData());

        static::assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '验证器规则支持数组写法:每一项都是一个数组(第一个是规则，第一个是参数非数组兼容为数组)',
        'zh-CN:description' => <<<'EOT'
可以通过 `success` 判断是否通过验证，`error` 返回错误消息。
EOT,
    ])]
    public function testRuleIsArray2(): void
    {
        $validate = new Validator(
            [
                'name' => '小牛哥',
            ],
            [
                'name' => ['required', ['max_length', 10]],
            ],
            [
                'name' => '用户名',
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

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame([], $validate->error());
        static::assertSame([], $validate->getMessage());
        static::assertSame(['name' => '小牛哥'], $validate->getData());

        static::assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '验证器规则支持数组写法:每一项都是一个数组(第一个是规则，第一个是参数数组用法)',
        'zh-CN:description' => <<<'EOT'
可以通过 `success` 判断是否通过验证，`error` 返回错误消息。
EOT,
    ])]
    public function testRuleIsArray3(): void
    {
        $validate = new Validator(
            [
                'name' => '小牛哥',
            ],
            [
                'name' => ['required', ['max_length', [10]]],
            ],
            [
                'name' => '用户名',
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

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame([], $validate->error());
        static::assertSame([], $validate->getMessage());
        static::assertSame(['name' => '小牛哥'], $validate->getData());

        static::assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '验证器规则支持数组每一项字符串支持分隔:可以用于实际业务中合并验证规则的需求',
        'zh-CN:description' => <<<'EOT'
可以通过 `success` 判断是否通过验证，`error` 返回错误消息。
EOT,
    ])]
    public function testRuleIsArrayStringMixed(): void
    {
        $validate = new Validator(
            [
                'name' => '小牛哥',
            ],
            [
                'name' => ['required|chinese|min_length:1', ['max_length', [10]]],
            ],
            [
                'name' => '用户名',
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
                        "chinese",
                        []
                    ],
                    [
                        "min_length",
                        [
                            "1"
                        ]
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

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame([], $validate->error());
        static::assertSame([], $validate->getMessage());
        static::assertSame(['name' => '小牛哥'], $validate->getData());

        static::assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'make 创建验证器',
    ])]
    public function testMake(): void
    {
        $validate = Validator::make(
            [
                'name' => '小牛哥',
            ],
            [
                'name' => 'required|max_length:10',
            ],
            [
                'name' => '用户名',
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
                            "10"
                        ]
                    ]
                ]
            }
            eot;

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame([], $validate->error());
        static::assertSame([], $validate->getMessage());
        static::assertSame(['name' => '小牛哥'], $validate->getData());

        static::assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '验证器校验错误',
    ])]
    public function testError(): void
    {
        $validate = new Validator(
            [
                'name' => '小牛哥',
            ],
            [
                'name' => 'required|min_length:20',
            ],
            [
                'name' => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '设置校验数据',
    ])]
    public function testData(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
                'name' => 'required|min_length:20',
            ],
            [
                'name' => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->data(['name' => '12345678901234567890']);

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
    }

    #[Api([
        'zh-CN:title' => '添加校验数据',
    ])]
    public function testAddData(): void
    {
        $validate = new Validator(
            [
            ],
            [
                'name' => 'required|min_length:20|'.IValidator::OPTIONAL,
            ],
            [
                'name' => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());

        $validate->addData(['name' => '中国']);

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '设置校验规则',
    ])]
    public function testRule(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
            ],
            [
                'name' => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());

        $validate->rule(['name' => 'required|min_length:20']);

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->rule(['name' => 'required|max_length:20']);

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
    }

    #[Api([
        'zh-CN:title' => '设置校验规则支持条件',
        'zh-CN:description' => <<<'EOT'
第一个闭包条件参数不为空，如果闭包返回 `true` 则添加改验证规则，否则忽略。
EOT,
    ])]
    public function testRuleIf(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
            ],
            [
                'name' => '用户名',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());

        $validate->rule(['name' => 'required|min_length:20'], function (array $data) {
            $this->assertSame(['name' => '中国'], $data);

            return false;
        });

        $rule = <<<'eot'
            []
            eot;

        static::assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
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
                'name' => '用户名',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());

        $validate->rule(['name' => 'required|min_length:20'], function (array $data) {
            $this->assertSame(['name' => '中国'], $data);

            return true;
        });

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '添加校验规则',
    ])]
    public function testAddRule(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
            ],
            [
                'name' => '用户名',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());

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
                            "20"
                        ]
                    ]
                ]
            }
            eot;

        static::assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '添加校验规则支持条件',
        'zh-CN:description' => <<<'EOT'
第一个闭包条件参数不为空，如果闭包返回 `true` 则添加改验证规则，否则忽略。
EOT,
    ])]
    public function testAddRuleIf(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
            ],
            [
                'name' => '用户名',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());

        $validate->addRule(['name' => 'required|min_length:20'], function (array $data) {
            $this->assertSame(['name' => '中国'], $data);

            return false;
        });

        $rule = <<<'eot'
            []
            eot;

        static::assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
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
                'name' => '用户名',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());

        $validate->addRule(['name' => 'required|min_length:20'], function (array $data) {
            $this->assertSame(['name' => '中国'], $data);

            return true;
        });

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '设置验证消息',
    ])]
    public function testMessage(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
                'name' => 'required|min_length:20',
            ],
            [
                'name' => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        static::assertSame(
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

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '添加验证消息',
        'zh-CN:description' => <<<'EOT'
设置规则所有字段的验证消息。
EOT,
    ])]
    public function testAddMessage(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
                'name' => 'required|min_length:20',
            ],
            [
                'name' => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        static::assertSame(
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

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '添加指定字段验证规则消息',
        'zh-CN:description' => <<<'EOT'
可以单独为某个字段指定验证消息规则，其它字段验证消息保持不变。
EOT,
    ])]
    public function testAddMessageForOneField(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
                'name' => 'required|min_length:20',
            ],
            [
                'name' => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        static::assertSame(
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

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '添加指定字段验证规则消息(圆点分隔)',
        'zh-CN:description' => <<<'EOT'
通过圆点 `.` 分隔开来。
EOT,
    ])]
    public function testAddMessageForOneFieldSeparateByDot(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
                'name' => 'required|min_length:20',
            ],
            [
                'name' => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        static::assertSame(
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

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '添加指定多层子字段验证规则消息(圆点分隔)',
        'zh-CN:description' => <<<'EOT'
通过圆点 `.` 分隔开来。
EOT,
    ])]
    public function testSubDataWithSubMessage(): void
    {
        $validate = new Validator(
            [
                'name' => ['sub' => ['sub' => '']],
            ],
            [
                'name.sub.sub' => 'required|'.IValidator::MUST,
            ],
            [
                'name' => '歌曲',
            ]
        );

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name.sub.sub": [
                    "name.sub.sub 不能为空"
                ]
            }
            eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->addMessage(['name.sub.sub' => ['required' => '字段 {field} 不能为空']]);

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name.sub.sub": [
                    "字段 name.sub.sub 不能为空"
                ]
            }
            eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '添加通配符字段验证规则消息',
        'zh-CN:description' => <<<'EOT'
通过 `*` 来代表通配符。
EOT,
    ])]
    public function testWildcardSubDataWithSubMessage(): void
    {
        $validate = new Validator(
            [
                'name' => ['sub' => ['sub' => '']],
            ],
            [
                'name.sub.sub' => 'required|'.IValidator::MUST,
            ],
            [
                'name' => '歌曲',
            ]
        );

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name.sub.sub": [
                    "name.sub.sub 不能为空"
                ]
            }
            eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->addMessage(['name*' => ['required' => 'sub {field} must have value']]);

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name.sub.sub": [
                    "sub name.sub.sub must have value"
                ]
            }
            eot;

        static::assertSame(
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
                'name.sub.sub' => 'required|'.IValidator::MUST,
            ],
            [
                'name' => '歌曲',
            ]
        );

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name.sub.sub": [
                    "name.sub.sub 不能为空"
                ]
            }
            eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '设置验证字段隐射',
    ])]
    public function testName(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
                'name' => 'required|min_length:20',
            ],
            [
                'name' => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());
        static::assertSame(['name' => '用户名'], $validate->getName());

        static::assertSame(
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

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '添加验证字段隐射',
    ])]
    public function testAddName(): void
    {
        $validate = new Validator(
            [
                'name' => '中国',
            ],
            [
                'name' => 'required|min_length:20',
            ],
            [
                'name' => '用户名',
            ]
        );

        $error = <<<'eot'
            {
                "name": [
                    "用户名 不满足最小长度 20"
                ]
            }
            eot;

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());
        static::assertSame(['name' => '用户名'], $validate->getName());

        static::assertSame(
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

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '验证后回调',
        'zh-CN:description' => <<<'EOT'
无论成功或者失败都会执行回调。
EOT,
    ])]
    public function testAfter(): void
    {
        $validate = new Validator(
            [
                'name' => '成都',
            ],
            [
                'name' => 'required|max_length:10',
            ],
            [
                'name' => '地名',
            ]
        );

        $validate->after(function ($v): void {
            $this->assertSame(['name' => '地名'], $v->getName());
        });

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
    }

    #[Api([
        'zh-CN:title' => '自定义扩展验证规则',
    ])]
    public function testExtend(): void
    {
        $validate = new Validator(
            [
                'name' => 1,
            ],
            [
                'name' => 'required|custom_rule:10',
            ],
            [
                'name' => '地名',
            ]
        );

        $validate->extend('custom_rule', function ($value, array $param, IValidator $validator, string $field): bool {
            if (1 === $value) {
                return true;
            }

            return false;
        });

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());

        $validate->data(['name' => 0]);

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());
    }

    #[Api([
        'zh-CN:title' => '直接调用验证规则',
    ])]
    public function testCall(): void
    {
        $validate = new Validator();

        static::assertTrue($validate->minLength('成都', 1));
        static::assertTrue($validate->minLength('成都', 2));
        static::assertFalse($validate->minLength('成都', 3));
        static::assertFalse($validate->alpha('成都'));
        static::assertTrue($validate->alpha('cd'));
    }

    #[Api([
        'zh-CN:title' => '直接调用自定义验证规则',
    ])]
    public function testCallCustom(): void
    {
        $validate = new Validator();

        $validate->extend('custom_foo_bar', function (string $field, $value, array $param): bool {
            if ('成都' === $value) {
                return true;
            }

            return false;
        });

        static::assertTrue($validate->customFooBar('成都'));
        static::assertFalse($validate->customFooBar('魂之挽歌'));
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
                'name' => 'required|min_length',
            ],
            [
                'name' => '地名',
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
                'name' => 'Tests\\Validate\\NotFound',
            ],
            [
                'name' => '地名',
            ]
        );

        $validate->extend('tests\\validate\\not_found', 'Tests\\Validate\\NotFound');

        $validate->success();
    }

    #[Api([
        'zh-CN:title' => '自定义扩展验证规则(类)',
        'zh-CN:description' => <<<'EOT'
自定义扩展规则可以为一个独立的类，例如下面的例子。

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Validate\ExtendClassTest1::class)]}
```

默认情况下，此时自定义类的 `handle` 方法将作为验证入口。
EOT,
    ])]
    public function testCallExtendClass(): void
    {
        $validate = new Validator(
            [
                'name' => 1,
            ],
            [
                'name' => 'custom_foobar',
            ],
            [
                'name' => '地名',
            ]
        );

        $container = new Container();
        $validate->setContainer($container);
        $validate->extend('custom_foobar', ExtendClassTest1::class);
        static::assertTrue($validate->success());
        $validate->data(['name' => 'foo']);
        static::assertFalse($validate->success());
    }

    #[Api([
        'zh-CN:title' => '自定义扩展验证规则(类)，指定验证方法',
        'zh-CN:description' => <<<'EOT'
自定义扩展规则可以为一个独立的类，例如下面的例子。

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Validate\ExtendClassTest1::class)]}
```

指定方法情况下,通过 `@` 分隔开来，此时自定义类的 `handle2` 方法将作为验证入口。
EOT,
    ])]
    public function testCallExtendClassWithCustomMethod(): void
    {
        $validate = new Validator(
            [
                'name' => 2,
            ],
            [
                'name' => 'custom_foobar',
            ],
            [
                'name' => '地名',
            ]
        );

        $container = new Container();
        $validate->setContainer($container);
        $validate->extend('custom_foobar', ExtendClassTest1::class.'@handle2');
        static::assertTrue($validate->success());
        $validate->data(['name' => 'foo']);
        static::assertFalse($validate->success());
    }

    public function testCallExtendClassWithCustomMethod2(): void
    {
        $validate = new Validator(
            [
                'name' => 3,
            ],
            [
                'name' => 'custom_foobar',
            ],
            [
                'name' => '地名',
            ]
        );

        $container = new Container();
        $validate->setContainer($container);
        $validate->extend('custom_foobar', ExtendClassTest2::class);
        static::assertTrue($validate->success());
        $validate->data(['name' => 'foo']);
        static::assertFalse($validate->success());
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
                'name' => 'custom_foobar',
            ],
            [
                'name' => '地名',
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
                'name' => 'custom_foobar',
            ],
            [
                'name' => '地名',
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
                'name' => $skipRule,
            ],
            [
                'name' => '地名',
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function skipRuleProvider(): array
    {
        return [
            [IValidator::OPTIONAL],
            [IValidator::OPTIONAL_STRING],
            [IValidator::MUST],
            [IValidator::SKIP_SELF],
            [IValidator::SKIP_OTHER],
        ];
    }

    #[Api([
        'zh-CN:title' => '验证失败则跳过其它验证规则',
        'zh-CN:description' => <<<'EOT'
只需要在校验规则中加入 `SKIP_OTHER` 即可。
EOT,
    ])]
    public function testShouldSkipOther(): void
    {
        $validate = new Validator(
            [
                'name' => '',
                'value' => '',
            ],
            [
                'name' => 'required|alpha',
                'value' => 'required',
            ],
            [
                'name' => '地名',
                'value' => '值',
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

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());
        static::assertSame(['name' => '地名', 'value' => '值'], $validate->getName());

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->addRule(['name' => 'required|alpha|'.IValidator::SKIP_OTHER]);

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name": [
                    "地名 不能为空"
                ]
            }
            eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '验证失败则跳过自身其它验证规则',
        'zh-CN:description' => <<<'EOT'
只需要在校验规则中加入 `SKIP_SELF` 即可，只会跳过当前字段的其他验证规则，而其它字段的验证规则不受影响。
EOT,
    ])]
    public function testShouldSkipSelf(): void
    {
        $validate = new Validator(
            [
                'name' => '',
                'value' => '',
            ],
            [
                'name' => 'required|alpha',
                'value' => 'required',
            ],
            [
                'name' => '地名',
                'value' => '值',
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

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());
        static::assertSame(['name' => '地名', 'value' => '值'], $validate->getName());

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->addRule(['name' => 'required|alpha|'.IValidator::SKIP_SELF]);

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

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

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '值为 null 会跳过可选验证规则',
        'zh-CN:description' => <<<'EOT'
如果校验规则中有 `OPTIONAL` ，那么字段值为 `null` 则不会执行验证规则。
EOT,
    ])]
    public function testOptional(): void
    {
        $validate = new Validator(
            [
                'name' => null,
            ],
            [
                'name' => 'required|'.IValidator::OPTIONAL,
            ],
            [
                'name' => '地名',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame(['name' => '地名'], $validate->getName());
    }

    #[Api([
        'zh-CN:title' => '值为 null或者空字符串 会跳过可选字符串验证规则',
        'zh-CN:description' => <<<'EOT'
如果校验规则中有 `OPTIONAL_STRING` ，那么字段值为 `null` 或者空字符串则不会执行验证规则。
EOT,
    ])]
    public function testOptionalString(): void
    {
        $validate = new Validator(
            [
                'name' => null,
            ],
            [
                'name' => 'required|'.IValidator::OPTIONAL_STRING,
            ],
            [
                'name' => '地名',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame(['name' => '地名'], $validate->getName());

        $validate = new Validator(
            [
                'name' => '',
            ],
            [
                'name' => 'required|'.IValidator::OPTIONAL_STRING,
            ],
            [
                'name' => '地名',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame(['name' => '地名'], $validate->getName());
    }

    #[Api([
        'zh-CN:title' => '值为 null 默认必须验证',
        'zh-CN:description' => <<<'EOT'
我们加入 `MUST` 或者默认不指定，那么 `null` 也会执行验证。
EOT,
    ])]
    public function testMustRequired(): void
    {
        $validate = new Validator(
            [
                'name' => null,
            ],
            [
                'name' => 'required|'.IValidator::OPTIONAL,
            ],
            [
                'name' => '地名',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame(['name' => '地名'], $validate->getName());

        $validate->rule(['name' => 'required']);
        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name": [
                    "地名 不能为空"
                ]
            }
            eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate->data(['name' => null]);
        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());

        $error = <<<'eot'
            {
                "name": [
                    "地名 不能为空"
                ]
            }
            eot;

        static::assertSame(
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
                'name' => '',
                'nafoo' => '',
                'nabar' => '',
            ],
            [
                'name' => 'required',
                'nafoo' => 'required',
                'nabar' => 'required',
            ],
            [
                'name' => '地名',
                'nafoo' => 'foo',
                'nabar' => 'bar',
            ]
        );

        $validate->addMessage(['na*' => ['required' => 'test {field} required message']]);

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());
        static::assertSame(['name' => '地名', 'nafoo' => 'foo', 'nabar' => 'bar'], $validate->getName());

        $message = <<<'eot'
            {
                "name.required": "test {field} required message",
                "nafoo.required": "test {field} required message",
                "nabar.required": "test {field} required message"
            }
            eot;

        static::assertSame(
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

        static::assertSame(
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

        static::assertSame(
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

        static::assertSame(
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
                'name' => '',
                'nafoo' => '',
                'nabar' => '',
            ],
            [
                'name' => 'required',
                'nafoo' => 'required',
                'nabar' => 'required',
            ],
            [
                'name' => '地名',
                'nafoo' => 'foo',
                'nabar' => 'bar',
            ]
        );

        $validate->addMessage(['na*' => 'test {field} required message']);

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());
        static::assertSame(['name' => '地名', 'nafoo' => 'foo', 'nabar' => 'bar'], $validate->getName());

        $message = <<<'eot'
            {
                "name.required": "test {field} required message",
                "nafoo.required": "test {field} required message",
                "nabar.required": "test {field} required message"
            }
            eot;

        static::assertSame(
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

        static::assertSame(
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

        static::assertSame(
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

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error(),
                3
            )
        );
    }

    #[Api([
        'zh-CN:title' => '通配符验证规则支持',
        'zh-CN:description' => <<<'EOT'
可以通过 `*` 来表示通配符验证规则。
EOT,
    ])]
    public function testWildcardRule(): void
    {
        $validate = new Validator(
            [
                'name' => '',
                'nafoo' => '',
                'nabar' => '',
            ],
            [
            ],
            [
                'name' => '地名',
                'nafoo' => 'foo',
                'nabar' => 'bar',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());

        $validate->rule(['na*' => 'required']);

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());
        static::assertSame(['name' => '地名', 'nafoo' => 'foo', 'nabar' => 'bar'], $validate->getName());

        $data = <<<'eot'
            {
                "name": "",
                "nafoo": "",
                "nabar": ""
            }
            eot;

        static::assertSame(
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

        static::assertSame(
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

        static::assertSame(
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
                'name' => '歌曲',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame([['required', []]], $this->invokeTestMethod($validate, 'getFieldRule', ['name']));
        static::assertSame([], $this->invokeTestMethod($validate, 'getFieldRule', ['foo']));
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
                'name' => '歌曲',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
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
                'name' => '歌曲',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertTrue($this->invokeTestMethod($validate, 'hasFieldRuleWithParam', ['name', 'required']));
        static::assertFalse($this->invokeTestMethod($validate, 'hasFieldRuleWithParam', ['name', 'foo']));
        static::assertFalse($this->invokeTestMethod($validate, 'hasFieldRuleWithParam', ['bar', '']));
    }

    public function test1(): void
    {
        $e = new ValidatorException('hello');
        static::assertSame('hello', $e->getMessage());
        static::assertFalse($e->reportable());
    }

    #[Api([
        'zh-CN:title' => '类的静态方法验证规则支持',
        'zh-CN:description' => <<<'EOT'
可以直接指定类的静态方法为验证规则，例如下面的例子。

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Validate\ClassStaticDemo1::class)]}
```

指定方法情况下,通过 `@` 分隔开来，此时自定义类的 `demoValidator1` 方法将作为验证入口。
EOT,
    ])]
    public function test2(): void
    {
        $validate = new Validator(
            [
                'name' => 2,
            ],
            [
                'name' => [
                    ClassStaticDemo1::class.'@demoValidator1',
                ],
            ],
            [
                'name' => '地名',
            ]
        );

        $container = new Container();
        $validate->setContainer($container);
        static::assertTrue($validate->success());
        $validate->data(['name' => 'foo']);
        static::assertTrue($validate->success());
    }

    public function test3(): void
    {
        $validate = new Validator(
            [
                'name' => 2,
            ],
            [
                'name' => [
                    ClassStaticDemo1::class.'@demoValidator2',
                ],
            ],
            [
                'name' => '地名',
            ]
        );

        $container = new Container();
        $validate->setContainer($container);
        static::assertFalse($validate->success());
        $validate->data(['name' => 'foo']);
        static::assertFalse($validate->success());
    }

    public function test4(): void
    {
        $validate = new Validator(
            [
                'name' => 2,
            ],
            [
                'name' => [
                    ClassStaticDemo1::class,
                ],
            ],
            [
                'name' => '地名',
            ]
        );

        $container = new Container();
        $validate->setContainer($container);
        static::assertFalse($validate->success());
        $validate->data(['name' => 'foo']);
        static::assertFalse($validate->success());
    }

    public function test5(): void
    {
        $validate = new Validator(
            [
                'name' => 2,
            ],
            [
                'name' => [
                    ClassStaticDemo1::class.'@demoValidator3',
                ],
            ],
            [
                'name' => '地名',
            ]
        );

        $container = new Container();
        $validate->setContainer($container);
        static::assertFalse($validate->success());
        $error = <<<'eot'
{
    "name": [
        ""
    ]
}
eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
        $validate->data(['name' => 'foo']);
        static::assertTrue($validate->success());
    }

    #[Api([
        'zh-CN:title' => '类的静态方法验证规则支持通过异常抛出错误',
        'zh-CN:description' => <<<'EOT'
可以在类的静态方法中抛出异常消息，异常必须为\Leevel\Validate\ValidatorException，例如下面的例子。

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Validate\ClassStaticDemo1::class)]}
```

指定方法情况下,通过 `@` 分隔开来，此时自定义类的 `demoValidator4` 方法将作为验证入口。
EOT,
    ])]
    public function test6(): void
    {
        $validate = new Validator(
            [
                'name' => 2,
            ],
            [
                'name' => [
                    ClassStaticDemo1::class.'@demoValidator4',
                ],
            ],
            [
                'name' => '地名',
            ]
        );

        $container = new Container();
        $validate->setContainer($container);
        static::assertFalse($validate->success());
        $error = <<<'eot'
{
    "name": [
        "我的名字验证失败"
    ]
}
eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
        $validate->data(['name' => 'foo']);
        static::assertFalse($validate->success());
    }

    #[Api([
        'zh-CN:title' => '验证器支持反义规则',
        'zh-CN:description' => <<<'EOT'
定义反义规则，只需要在规则前面加上英文感叹号即可。
EOT,
    ])]
    public function test7(): void
    {
        $validate = new Validator(
            [
                'name' => 8,
            ],
            [
                'name' => '!min:5',
            ],
            [
                'name' => '地名',
            ]
        );

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());
        static::assertSame(['name' => '地名'], $validate->getName());
        $error = <<<'eot'
{
    "name": [
        "不满足【地名 值不能小于 5】"
    ]
}
eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
    }

    public function test8(): void
    {
        $validate = new Validator(
            [
                'name' => 8,
            ],
            [
                'name' => 'demo',
            ],
            [
                'name' => '地名',
            ]
        );

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());
        static::assertSame(['name' => '地名'], $validate->getName());
        $error = <<<'eot'
{
    "name": [
        "Demo is error."
    ]
}
eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate = new Validator(
            [
                'name' => 'demo',
            ],
            [
                'name' => 'demo',
            ],
            [
                'name' => '地名',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame(['name' => '地名'], $validate->getName());
    }

    public function test9(): void
    {
        $validate = new Validator(
            [
                'name' => 8,
            ],
            [
                'name' => 'demo_two',
            ],
            [
                'name' => '地名',
            ]
        );

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());
        static::assertSame(['name' => '地名'], $validate->getName());
        $error = <<<'eot'
{
    "name": [
        "Demo is error."
    ]
}
eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );

        $validate = new Validator(
            [
                'name' => 'demo',
            ],
            [
                'name' => 'demo_two',
            ],
            [
                'name' => '地名',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame(['name' => '地名'], $validate->getName());
    }

    public function test10(): void
    {
        $validate = new Validator(
            [
                'name' => 'demo2',
            ],
            [
                'name' => '!demo_two',
            ],
            [
                'name' => '地名',
            ]
        );

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame(['name' => '地名'], $validate->getName());
    }

    public function test11(): void
    {
        $validate = new Validator(
            [
                'name' => 8,
            ],
            [
                'name' => 'demo_three',
            ],
            [
                'name' => '地名',
            ]
        );

        static::assertFalse($validate->success());
        static::assertTrue($validate->fail());
        static::assertSame(['name' => '地名'], $validate->getName());
        $error = <<<'eot'
{
    "name": [
        "Demo is error."
    ]
}
eot;

        static::assertSame(
            $error,
            $this->varJson(
                $validate->error()
            )
        );
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

class ClassStaticDemo1
{
    public static function handle(mixed $value, array $param, Validator $validator): bool
    {
        return false;
    }

    public static function demoValidator1(mixed $value, array $param, Validator $validator): bool
    {
        return true;
    }

    public static function demoValidator2(mixed $value, array $param, Validator $validator): bool
    {
        return false;
    }

    public static function demoValidator3(mixed $value, array $param, Validator $validator): bool
    {
        return 'foo' === $value;
    }

    public static function demoValidator4(mixed $value, array $param, Validator $validator): bool
    {
        throw new ValidatorException('我的名字验证失败');
    }
}
