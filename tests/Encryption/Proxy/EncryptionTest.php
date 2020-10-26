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

namespace Tests\Encryption\Proxy;

use Leevel\Di\Container;
use Leevel\Encryption\Encryption;
use Leevel\Encryption\Proxy\Encryption as ProxyEncryption;
use Tests\TestCase;

class EncryptionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $encryption = $this->createEncryption($container);
        $container->singleton('encryption', function () use ($encryption): Encryption {
            return $encryption;
        });

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
            'encode-key',
            $this->getTestProperty($encryption, 'key')
        );
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $encryption = $this->createEncryption($container);
        $container->singleton('encryption', function () use ($encryption): Encryption {
            return $encryption;
        });

        $sourceMessage = '123456';
        $encodeMessage = ProxyEncryption::encrypt($sourceMessage);
        $this->assertFalse($sourceMessage === $encodeMessage);
        $this->assertSame(
            ProxyEncryption::decrypt($encodeMessage),
            $sourceMessage
        );
        $this->assertSame(
            ProxyEncryption::decrypt($encodeMessage.'foo'),
            ''
        );
        $this->assertSame(
            'encode-key',
            $this->getTestProperty($encryption, 'key')
        );
    }

    protected function createEncryption(): Encryption
    {
        return new Encryption('encode-key');
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
