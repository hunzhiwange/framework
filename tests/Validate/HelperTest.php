<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Validate;

use Leevel\Validate\Helper;
use Tests\TestCase;

/**
 * @api(
 *     title="验证助手函数",
 *     path="component/validate/helper",
 *     description="框架提供助手函数来提供简洁的校验服务，助手的规则与验证器共享校验规则。",
 * )
 */
class HelperTest extends TestCase
{
    /**
     * @api(
     *     title="助手基础功能",
     *     description="",
     *     note="",
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
