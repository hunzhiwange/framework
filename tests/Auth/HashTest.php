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

namespace Tests\Auth;

use Leevel\Auth\Hash;
use Tests\TestCase;

/**
 * hash test.
 */
class HashTest extends TestCase
{
    protected function setUp(): void
    {
        if (isset($_SERVER['SUDO_USER']) &&
            'vagrant' === $_SERVER['SUDO_USER']) {
            $this->markTestSkipped('Ignore hash error.');
        }
    }

    public function testBaseUse(): void
    {
        $hash = new Hash();

        $hashPassword = $hash->password('123456');
        $this->assertTrue($hash->verify('123456', $hashPassword));
    }

    public function testWithCost(): void
    {
        $hash = new Hash();

        $hashPassword = $hash->password('123456', ['cost' => 12]);
        $this->assertTrue($hash->verify('123456', $hashPassword));
    }
}
