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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Encryption;

use Leevel\Encryption\Encryption;
use Leevel\Encryption\IEncryption;
use Tests\TestCase;

/**
 * encryption test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.11
 *
 * @version 1.0
 */
class EncryptionTest extends TestCase
{
    public function testBaseUse()
    {
        $encryption = new Encryption('encode-key');

        $this->assertInstanceof(IEncryption::class, $encryption);

        $sourceMessage = '123456';

        $encodeMessage = $encryption->encrypt($sourceMessage);

        $this->assertFalse($sourceMessage === $encodeMessage);

        $this->assertSame(
            $encryption->decrypt($encodeMessage),
            $sourceMessage
        );

        $this->assertSame(
            'encode-key',
            $this->getTestProperty($encryption, 'key')
        );

        $this->assertSame(
            0,
            $this->getTestProperty($encryption, 'expiry')
        );
    }
}
