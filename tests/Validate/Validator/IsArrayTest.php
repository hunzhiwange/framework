<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Kernel\Utils\Api;
use Leevel\Validate\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.验证是否为数组',
    'path' => 'validate/validator/is-array',
])]
final class IsArrayTest extends TestCase
{
    #[DataProvider('provideBaseUseCases')]
    #[Api([
        'zh-CN:title' => '验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\IsArrayTest::class, 'baseUseProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBaseUse($value): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'is_array',
            ]
        );

        self::assertTrue($validate->success());
    }

    public static function provideBaseUseCases(): iterable
    {
        return [
            [['this', 'is', 'an array']],
            [['a' => 'aaa', 'b' => 1, 'c' => true]],
            [['im', 'an', 'array']],
            [['im' => 'not', 'going' => 'to be', 'an' => 'array']],
        ];
    }

    #[DataProvider('provideBadCases')]
    #[Api([
        'zh-CN:title' => '未验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是未通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\IsArrayTest::class, 'badProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBad($value): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'is_array',
            ]
        );

        self::assertFalse($validate->success());
    }

    public static function provideBadCases(): iterable
    {
        return [
            ['this is a string'],
            [0.52148389816284],
            ['0.0'],
            ['-0.0'],
            ['foo'],
            ['bar'],
            ['hello'],
            ['world'],
        ];
    }
}
