<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Kernel\Utils\Api;
use Leevel\Validate\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.验证是否都是小写',
    'path' => 'validate/validator/alpha-lower',
])]
final class AlphaLowerTest extends TestCase
{
    #[DataProvider('provideBaseUseCases')]
    #[Api([
        'zh-CN:title' => '验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\AlphaLowerTest::class, 'baseUseProvider')]}
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
                'name' => 'alpha_lower',
            ]
        );

        self::assertTrue($validate->success());
    }

    public static function provideBaseUseCases(): iterable
    {
        return [
            ['abc'],
        ];
    }

    #[DataProvider('provideBadCases')]
    #[Api([
        'zh-CN:title' => '未验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是未通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\AlphaLowerTest::class, 'badProvider')]}
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
                'name' => 'alpha_lower',
            ]
        );

        self::assertFalse($validate->success());
    }

    public static function provideBadCases(): iterable
    {
        return [
            [' '],
            ['ABC'],
            ['aBc'],
            ['not numeric'],
            [new \stdClass()],
            [['foo', 'bar']],
            [[1, 2]],
            ['this is a string'],
            [true],
            [1],
            [[[], []]],
        ];
    }
}
