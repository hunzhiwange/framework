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

namespace Tests\Http;

use Leevel\Http\RedirectResponse;
use Leevel\Session\Session;
use Tests\TestCase;

/**
 * RedirectResponse test
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.03.14
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class RedirectResponseTest extends TestCase
{
    public function testGenerateMetaRedirect()
    {
        $response = new RedirectResponse('foo.bar');

        $this->assertSame(1, preg_match(
            '#<meta http-equiv="refresh" content="\d+;url=foo\.bar" />#',
            preg_replace(['/\s+/', '/\'/'], [' ', '"'], $response->getContent())
        ));
    }

    public function testRedirectResponseConstructorNullUrl()
    {
        $this->expectException(\InvalidArgumentException::class);

        $response = new RedirectResponse(null);
    }

    public function testRedirectResponseConstructorWrongStatusCode()
    {
        $this->expectException(\InvalidArgumentException::class);

        $response = new RedirectResponse('foo.bar', 404);
    }

    public function testGenerateLocationHeader()
    {
        $response = new RedirectResponse('foo.bar');
        $this->assertTrue($response->headers->has('Location'));
        $this->assertSame('foo.bar', $response->headers->get('Location'));
    }

    public function testGetTargetUrl()
    {
        $response = new RedirectResponse('foo.bar');
        $this->assertSame('foo.bar', $response->getTargetUrl());
    }

    public function testSetTargetUrl()
    {
        $response = new RedirectResponse('foo.bar');
        $response->setTargetUrl('baz.beep');
        $this->assertSame('baz.beep', $response->getTargetUrl());
    }

    public function testSetTargetUrlNull()
    {
        $this->expectException(\InvalidArgumentException::class);

        $response = new RedirectResponse('foo.bar');
        $response->setTargetUrl(null);
    }

    public function testCreate()
    {
        $response = RedirectResponse::create('foo', 301);
        $this->assertInstanceOf('Leevel\Http\RedirectResponse', $response);
        $this->assertSame(301, $response->getStatusCode());
    }

    public function testWith()
    {
        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWith());
        $this->assertInstanceOf('Leevel\Session\Session', $response->getSession());

        $response->with('foo', 'bar');
        $this->assertSame($response->getSession()->getFlash('foo'), 'bar');

        $data = ['myinput', 'world'];
        $response->setSession($this->mokeSessionArrayForWith());
        $response->withInput($data);
        $this->assertSame($response->getSession()->getFlash('inputs'), $data);
    }

    public function testWithError()
    {
        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWithError());
        $this->assertInstanceOf('Leevel\Session\Session', $response->getSession());

        $errorsDefault = [
            'name' => 'less than 6',
            'age'  => 'must be 18',
        ];

        $errorsCustom = [
            'foo' => 'bar is error',
        ];

        $response->withErrors($errorsDefault);
        $response->withErrors($errorsCustom, 'custom');

        $this->assertSame($response->getSession()->getFlash('errors'), [
            'default' => $errorsDefault,
            'custom'  => $errorsCustom,
        ]);
    }

    protected function mokeSessionForWith()
    {
        $session = $this->createMock(Session::class);

        $session->

        method('flash')->

        willReturn(null);

        $session->

        method('getFlash')->

        willReturn('bar');

        return $session;
    }

    protected function mokeSessionArrayForWith()
    {
        $session = $this->createMock(Session::class);

        $session->

        method('flash')->

        willReturn(null);

        $session->

        method('getFlash')->

        willReturn(['myinput', 'world']);

        return $session;
    }

    protected function mokeSessionForWithError()
    {
        $session = $this->createMock(Session::class);

        $session->

        method('flash')->

        willReturn(null);

        $session->

        method('getFlash')->

        willReturn([
            'default' => [
                'name' => 'less than 6',
                'age'  => 'must be 18',
            ],
            'custom' => [
                'foo' => 'bar is error',
            ],
        ]
        );

        return $session;
    }
}
