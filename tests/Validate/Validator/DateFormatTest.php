<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Kernel\Utils\Api;
use Leevel\Validate\Validator;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.是否为时间',
    'path' => 'validate/validator/dateformat',
])]
final class DateFormatTest extends TestCase
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
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\DateFormatTest::class, 'baseUseProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBaseUse($value, string $format): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'date_format:'.$format,
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            ['6.1.2018 13:00+01:00', 'j.n.Y H:iP'],
            ['15-Mar-2018', 'j-M-Y'],
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
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\DateFormatTest::class, 'badProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBad($value, string $format): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'date_format:'.$format,
            ]
        );

        static::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            ['2018.6.1 13:00+01:00', 'j.n.Y H:iP'],
            ['29/Feb/23:2018:59:31', 'd/M/Y:H:i:s'],
            [1, 'Y-m-d H:i:s'],
        ];
    }

    #[Api([
        'zh-CN:title' => 'date_format 参数缺失',
    ])]
    public function testMissParam(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Missing the first element of param.'
        );

        $validate = new Validator(
            [
                'name' => '',
            ],
            [
                'name' => 'date_format',
            ]
        );

        $validate->success();
    }
}
