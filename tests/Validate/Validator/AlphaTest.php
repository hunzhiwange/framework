<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Kernel\Utils\Api;
use Leevel\Validate\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.是否为英文字母',
    'path' => 'validate/validator/alpha',
])]
final class AlphaTest extends TestCase
{
    #[DataProvider('baseUseProvider')]
    #[Api([
        'zh-CN:title' => '验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\AlphaTest::class, 'baseUseProvider')]}
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
                'name' => 'alpha',
            ]
        );

        self::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            ['abc'],
            ['ABC'],
            ['Abc'],
        ];
    }

    #[DataProvider('badProvider')]
    #[Api([
        'zh-CN:title' => '未验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是未通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\AlphaTest::class, 'badProvider')]}
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
                'name' => 'alpha',
            ]
        );

        self::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            [' '],
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
