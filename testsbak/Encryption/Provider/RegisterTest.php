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

namespace Tests\Encryption\Provider;

use Leevel\Di\Container;
use Leevel\Encryption\IEncryption;
use Leevel\Encryption\Provider\Register;
use Leevel\Option\Option;
use Tests\TestCase;

/**
 * register test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.26
 *
 * @version 1.0
 */
class RegisterTest extends TestCase
{
    public function testBaseUse()
    {
        $test = new Register($container = $this->createContainer());

        $test->register();

        $encryption = $container->make('encryption');

        $this->assertInstanceof(IEncryption::class, $encryption);

        $sourceMessage = '123456';

        $encodeMessage = $encryption->encrypt($sourceMessage);

        $this->assertFalse($sourceMessage === $encodeMessage);

        $this->assertSame(
            $encryption->decrypt($encodeMessage),
            $sourceMessage
        );

        $this->assertSame(
            $encryption->decrypt($encodeMessage.'foo'),
            ''
        );

        $this->assertSame(
            '7becb888f518b20224a988906df51e05',
            $this->getTestProperty($encryption, 'key')
        );

        $this->assertSame(
            0,
            $this->getTestProperty($encryption, 'expiry')
        );
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $option = new Option([
            'app' => [
                'auth_key'    => '7becb888f518b20224a988906df51e05',
                'auth_expiry' => 0,
            ],
        ]);

        $container->singleton('option', $option);

        return $container;
    }
}
