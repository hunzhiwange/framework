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

namespace Tests\Cookie;

use Leevel\Cookie\Cookie;
use Leevel\Cookie\ICookie;
use Tests\TestCase;

/**
 * cookie test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.03
 *
 * @version 1.0
 * @coversNothing
 */
class CookieTest extends TestCase
{
    public function testBaseUse()
    {
        $cookie = new Cookie();

        $this->assertInstanceOf(ICookie::class, $cookie);

        $cookie->set('foo', 'bar');

        $this->assertSame([
            'q_foo' => [
                'q_foo',
                'bar',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
        ], $cookie->all());
    }

    public function testSetGetDelete()
    {
        $cookie = new Cookie();

        $cookie->set('foo', 'bar');

        $this->assertSame([
            'q_foo',
            'bar',
            time() + 86400,
            '/',
            '',
            false,
            false,
        ], $cookie->get('foo'));

        $cookie->delete('foo');

        $this->assertSame([
            'q_foo',
            null,
            time() + 86400,
            '/',
            '',
            false,
            false,
        ], $cookie->get('foo'));
    }

    public function testClear()
    {
        $cookie = new Cookie();

        $cookie->set('hello', 'world');
        $cookie->set('foo', 'bar');

        $this->assertSame([
            'q_hello' => [
                'q_hello',
                'world',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
            'q_foo' => [
                'q_foo',
                'bar',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
        ], $cookie->all());

        $cookie->clear();

        $this->assertSame([
            'q_hello' => [
                'q_hello',
                null,
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
            'q_foo' => [
                'q_foo',
                null,
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
        ], $cookie->all());
    }
}
