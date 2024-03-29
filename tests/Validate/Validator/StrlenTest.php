<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Kernel\Utils\Api;
use Leevel\Validate\Validator;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.长度验证',
    'path' => 'validate/validator/strlen',
])]
final class StrlenTest extends TestCase
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
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\StrlenTest::class, 'baseUseProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBaseUse($value, int $length): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'strlen:'.$length,
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            ['http://www.google.com', 21],
            ['http://queryphp.com', 19],
            ['foobar', 6],
            ['helloworld', 10],
            ['中国', 6],
            ['成都no1', 9],
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
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\StrlenTest::class, 'badProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBad($value, int $length): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'strlen:'.$length,
            ]
        );

        static::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            ['not numeric', 21],
            [[], 21],
            [new \stdClass(), 21],
            [['foo', 'bar'], 21],
            [[1, 2], 21],
            ['tel:+1-816-555-1212', 21],
            ['foo', 21],
            ['bar', 21],
            ['urn:oasis:names:specification:docbook:dtd:xml:4.1.2', 21],
            ['world', 21],
            [null, 21],
        ];
    }

    #[Api([
        'zh-CN:title' => 'strlen 参数缺失',
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
                'name' => 'strlen',
            ]
        );

        $validate->success();
    }
}
