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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Auth;

use Leevel\Auth\Hash;
use Tests\TestCase;

/**
 * hash test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.11.19
 *
 * @version 1.0
 */
class HashTest extends TestCase
{
    public function testBaseUse()
    {
        $hash = new Hash();

        $hashPassword = $hash->password('123456');
        $this->assertTrue($hash->verify('123456', $hashPassword));
    }

    public function testWithCost()
    {
        $hash = new Hash();

        $hashPassword = $hash->password('123456', ['cost' => 12]);
        $this->assertTrue($hash->verify('123456', $hashPassword));
    }
}
