<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Kernel\Utils\Api;
use Leevel\Validate\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.是否为日期',
    'path' => 'validate/validator/date',
])]
final class DateTest extends TestCase
{
    #[DataProvider('baseUseProvider')]
    #[Api([
        'zh-CN:title' => '验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\DateTest::class, 'baseUseProvider')]}
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
                'name' => 'date',
            ]
        );

        self::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            [new \DateTime()],
            [new \DateTime('2014-05-04')],
            ['2018-08-12'],
            ['2018-08'],
        ];
    }

    #[DataProvider('badProvider')]
    #[Api([
        'zh-CN:title' => '未验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是未通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\DateTest::class, 'badProvider')]}
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
                'name' => 'date',
            ]
        );

        self::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            ['2018'],
            ['2018-44-22'],
            ['2018-12-42'],
            [''],
            [[1, 2]],
        ];
    }
}
