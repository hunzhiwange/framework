<?php

declare(strict_types=1);

namespace Tests\Validate;

use Leevel\Validate\Helper;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="验证助手函数",
 *     path="validate/helper",
 *     zh-CN:description="框架提供助手函数来提供简洁的校验服务，助手的规则与验证器共享校验规则。",
 * )
 */
class HelperTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="助手基础功能",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $this->assertTrue(Helper::required(5));
        $this->assertTrue(Helper::required(0));
        $this->assertFalse(Helper::required(''));
    }

    public function testHelperNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Call to undefined function Leevel\\Validate\\Helper\\not_found()'
        );

        $this->assertFalse(Helper::notFound());
    }
}
