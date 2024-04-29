<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Kernel\Utils\Api;
use Leevel\Validate\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.是否为中国邮政编码',
    'path' => 'validate/validator/zipcode',
])]
final class ZipCodeTest extends TestCase
{
    #[DataProvider('baseUseProvider')]
    #[Api([
        'zh-CN:title' => '验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\ZipCodeTest::class, 'baseUseProvider')]}
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
                'name' => 'zip_code',
            ]
        );

        self::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            ['610000'],
            ['610200'],
            ['611900'],
            ['611500'],
            [611900],
            [611500],
        ];
    }

    #[DataProvider('badProvider')]
    #[Api([
        'zh-CN:title' => '未验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是未通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\ZipCodeTest::class, 'badProvider')]}
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
                'name' => 'zip_code',
            ]
        );

        self::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            ['9995031975011115028819'],
            [' '],
            ['not numeric'],
            [new \stdClass()],
            [['foo', 'bar']],
            [[1, 2]],
            ['this is a string'],
            [true],
            [[[], []]],
            ['not/numeric'],
            ['not?numeric'],
        ];
    }
}
