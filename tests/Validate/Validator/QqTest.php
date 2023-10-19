<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.是否为 QQ 号码',
    'path' => 'validate/validator/qq',
])]
final class QqTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed $value
     */
    #[Api([
        'zh-CN:title' => '验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\QqTest::class, 'baseUseProvider')]}
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
                'name' => 'qq',
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            ['10000'],
            ['1234567'],
            ['6344433'],
            ['123456789011'],
            [6344433],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed $value
     */
    #[Api([
        'zh-CN:title' => '未验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是未通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\QqTest::class, 'badProvider')]}
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
                'name' => 'qq',
            ]
        );

        static::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            ['9995031975011115028819'],
            ['1234567890111'],
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
