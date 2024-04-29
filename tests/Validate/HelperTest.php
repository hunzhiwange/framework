<?php

declare(strict_types=1);

namespace Tests\Validate;

use Leevel\Kernel\Utils\Api;
use Leevel\Validate\Helper;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证助手函数',
    'path' => 'validate/helper',
    'zh-CN:description' => <<<'EOT'
框架提供助手函数来提供简洁的校验服务，助手的规则与验证器共享校验规则。
EOT,
])]
final class HelperTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '助手基础功能',
    ])]
    public function testBaseUse(): void
    {
        self::assertTrue(Helper::required(5));
        self::assertTrue(Helper::required(0));
        self::assertFalse(Helper::required(''));
    }

    public function testHelperNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Class "Leevel\\Validate\\Helper\\NotFound" not found'
        );

        self::assertFalse(Helper::notFound());
    }
}
