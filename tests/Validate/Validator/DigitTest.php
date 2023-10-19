<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.检测字符串中的字符是否都是数字，负数和小数会检测不通过',
    'path' => 'validate/validator/digit',
])]
final class DigitTest extends TestCase
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
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\DigitTest::class, 'baseUseProvider')]}
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
                'name' => 'digit',
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            ['01'],
            ['0'],
            ['10002'],
            ['42'],
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
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\DigitTest::class, 'badProvider')]}
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
                'name' => 'digit',
            ]
        );

        static::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            ['1820.20'],
            ['wsl!12'],
            [-42],
            [42],
            ['2018-12-42'],
            [''],
            [[1, 2]],
        ];
    }
}
