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

namespace Tests\Encryption;

use Leevel\Di\Container;
use Leevel\Encryption\Helper;
use Leevel\Encryption\IEncryption;
use Tests\TestCase;

/**
 * helper test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.10
 *
 * @version 1.0
 */
class HelperTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testEncryptAndEecrypt(): void
    {
        $encryption = $this->createMock(IEncryption::class);
        $encryption->method('encrypt')->willReturn('foobar-helloworld');
        $this->assertSame('foobar-helloworld', $encryption->encrypt('foo', 3600));
        $encryption->method('decrypt')->willReturn('foo');
        $this->assertSame('foo', $encryption->decrypt('foobar-helloworld'));

        $container = $this->createContainer();
        $container->singleton('encryption', function () use ($encryption) {
            return $encryption;
        });

        $this->assertSame('foobar-helloworld', f('Leevel\\Encryption\\Helper\\encrypt', 'foo', 3600));
        $this->assertSame('foo', f('Leevel\\Encryption\\Helper\\decrypt', 'foobar-helloworld'));
    }

    public function testEncryptAndEecryptHelper(): void
    {
        $encryption = $this->createMock(IEncryption::class);
        $encryption->method('encrypt')->willReturn('foobar-helloworld');
        $this->assertSame('foobar-helloworld', $encryption->encrypt('foo', 3600));
        $encryption->method('decrypt')->willReturn('foo');
        $this->assertSame('foo', $encryption->decrypt('foobar-helloworld'));

        $container = $this->createContainer();
        $container->singleton('encryption', function () use ($encryption) {
            return $encryption;
        });

        $this->assertSame('foobar-helloworld', Helper::encrypt('foo', 3600));
        $this->assertSame('foo', Helper::decrypt('foobar-helloworld'));
    }

    public function testHelperNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Call to undefined function Leevel\\Encryption\\Helper\\not_found()'
        );

        $this->assertFalse(Helper::notFound());
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
