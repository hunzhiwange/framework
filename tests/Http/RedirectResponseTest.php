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

namespace Tests\Http;

use Leevel\Http\IRequest;
use Leevel\Http\RedirectResponse;
use Leevel\Session\ISession;
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
    public function testGenerateMetaRedirect(): void
    {
        $response = new RedirectResponse('foo.bar');

        $this->assertSame(1, preg_match(
            '#<meta http-equiv="refresh" content="\d+;url=foo\.bar" />#',
            preg_replace(['/\s+/', '/\'/'], [' ', '"'], $response->getContent())
        ));
    }

    public function testRedirectResponseConstructorWrongStatusCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The HTTP status code is not a redirect (404 given).');

        $response = new RedirectResponse('foo.bar', 404);
    }

    public function testGenerateLocationHeader(): void
    {
        $response = new RedirectResponse('foo.bar');
        $this->assertTrue($response->headers->has('Location'));
        $this->assertSame('foo.bar', $response->headers->get('Location'));
    }

    public function testGetTargetUrl(): void
    {
        $response = new RedirectResponse('foo.bar');
        $this->assertSame('foo.bar', $response->getTargetUrl());
    }

    public function testSetTargetUrl(): void
    {
        $response = new RedirectResponse('foo.bar');
        $response->setTargetUrl('baz.beep');
        $this->assertSame('baz.beep', $response->getTargetUrl());
    }

    public function testSetTargetUrlEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot redirect to an empty URL.');

        $response = new RedirectResponse('foo.bar');
        $response->setTargetUrl('');
    }

    public function testCreate(): void
    {
        $response = RedirectResponse::create('foo', 301);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(301, $response->getStatusCode());
    }

    public function testWith(): void
    {
        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWith());
        $this->assertInstanceOf(ISession::class, $response->getSession());

        $response->with('foo', 'bar');
        $this->assertSame($response->getSession()->getFlash('foo'), 'bar');
    }

    public function testWithInput(): void
    {
        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWith());
        $this->assertInstanceOf(ISession::class, $response->getSession());

        $data = ['myinput' => 'world'];
        $response->setSession($this->mokeSessionArrayForWith());
        $response->withInput($data);
        $this->assertSame($response->getSession()->getFlash('inputs'), $data);
    }

    public function testWithError(): void
    {
        $errorsDefault = [
            'name' => 'less than 6',
            'age'  => 'must be 18',
        ];

        $errorsCustom = [
            'foo' => 'bar is error',
        ];
        $data = ['default' => $errorsDefault, 'custom' => $errorsCustom];
        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWithError($data));
        $this->assertInstanceOf(ISession::class, $response->getSession());
        $response->withErrors($errorsDefault);
        $response->withErrors($errorsCustom, 'custom');

        $this->assertSame($response->getSession()->getFlash('errors'), $data);
    }

    public function testWithErrorFlow(): void
    {
        $condition = false;
        $errorsDefault = [
            'name' => 'less than 6',
            'age'  => 'must be 18',
        ];

        $errorsCustom = [
            'foo' => 'bar is error',
        ];

        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWithError(false === $condition ? $errorsCustom : $errorsDefault));
        $this->assertInstanceOf(ISession::class, $response->getSession());

        $response
            ->if($condition)
            ->withErrors($errorsDefault)
            ->else()
            ->withErrors($errorsCustom, 'custom')
            ->fi();
        $this->assertSame($response->getSession()->getFlash('errors'), $errorsCustom);
    }

    public function testWithErrorFlow2(): void
    {
        $condition = true;
        $errorsDefault = [
            'name' => 'less than 6',
            'age'  => 'must be 18',
        ];

        $errorsCustom = [
            'foo' => 'bar is error',
        ];

        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWithError(false === $condition ? $errorsCustom : $errorsDefault));
        $this->assertInstanceOf(ISession::class, $response->getSession());

        $response
            ->if($condition)
            ->withErrors($errorsDefault)
            ->else()
            ->withErrors($errorsCustom, 'custom')
            ->fi();
        $this->assertSame($response->getSession()->getFlash('errors'), $errorsDefault);
    }

    public function testSetTargetUrlFlow(): void
    {
        $condition = false;

        $response = new RedirectResponse('foo.bar');

        $response
            ->if($condition)
            ->setTargetUrl('foo')
            ->else()
            ->setTargetUrl('bar')
            ->fi();

        $this->assertSame('bar', $response->getTargetUrl());
    }

    public function testSetTargetUrlFlow2(): void
    {
        $condition = true;

        $response = new RedirectResponse('foo.bar');

        $response
            ->if($condition)
            ->setTargetUrl('foo')
            ->else()
            ->setTargetUrl('bar')
            ->fi();

        $this->assertSame('foo', $response->getTargetUrl());
    }

    public function testWithFlow(): void
    {
        $condition = false;

        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWith(false === $condition ? 'bar2' : 'bar'));
        $this->assertInstanceOf(ISession::class, $response->getSession());

        $response
            ->if($condition)
            ->with('foo', 'bar')
            ->else()
            ->with('foo', 'bar2')
            ->fi();
        $this->assertSame($response->getSession()->getFlash('foo'), 'bar2');
    }

    public function testWithFlow2(): void
    {
        $condition = true;

        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWith(false === $condition ? 'bar2' : 'bar'));
        $this->assertInstanceOf(ISession::class, $response->getSession());

        $response
            ->if($condition)
            ->with('foo', 'bar')
            ->else()
            ->with('foo', 'bar2')
            ->fi();
        $this->assertSame($response->getSession()->getFlash('foo'), 'bar');
    }

    public function testWithInputFlow(): void
    {
        $condition = false;
        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWith());
        $this->assertInstanceOf(ISession::class, $response->getSession());

        $data = ['myinput' => 'world'];
        $data2 = ['myinput2' => 'world2'];
        $response->setSession($this->mokeSessionArrayForWith(false === $condition ? $data2 : $data));
        $response
            ->if($condition)
            ->withInput($data)
            ->else()
            ->withInput($data2)
            ->fi();
        $this->assertSame($response->getSession()->getFlash('inputs'), $data2);
    }

    public function testWithInputFlow2(): void
    {
        $condition = true;
        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWith());
        $this->assertInstanceOf(ISession::class, $response->getSession());

        $data = ['myinput', 'world'];
        $data2 = ['myinput2', 'world2'];
        $response->setSession($this->mokeSessionArrayForWith(false === $condition ? $data2 : $data));
        $response
            ->if($condition)
            ->withInput($data)
            ->else()
            ->withInput($data2)
            ->fi();
        $this->assertSame($response->getSession()->getFlash('inputs'), $data);
    }

    public function testSetRequest(): void
    {
        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWith());
        $this->assertInstanceOf(ISession::class, $response->getSession());
        $this->assertNull($response->getRequest());

        $response->setRequest($this->mockRequest([], []));
        $this->assertInstanceOf(IRequest::class, $response->getRequest());
    }

    public function testOnlyInput(): void
    {
        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWith());
        $this->assertInstanceOf(ISession::class, $response->getSession());
        $this->assertNull($response->getRequest());

        $response->setRequest($this->mockRequest(['foo' => 'bar'], []));
        $this->assertInstanceOf(IRequest::class, $request = $response->getRequest());
        $response->onlyInput('foo');
        $this->assertSame(['foo' => 'bar'], $request->only(['foo']));
        $this->assertSame($response->getSession()->getFlash('foo'), 'bar');
    }

    public function testOnlyInputMustHasAtLeastOneArg(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Method onlyInput need at least one arg.');

        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWith());
        $this->assertInstanceOf(ISession::class, $response->getSession());
        $this->assertNull($response->getRequest());

        $response->setRequest($this->mockRequest(['foo' => 'bar'], []));
        $this->assertInstanceOf(IRequest::class, $request = $response->getRequest());
        $response->onlyInput();
    }

    public function testExceptInput(): void
    {
        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWith());
        $this->assertInstanceOf(ISession::class, $response->getSession());
        $this->assertNull($response->getRequest());

        $response->setRequest($this->mockRequest(['foo' => 'bar'], []));
        $this->assertInstanceOf(IRequest::class, $request = $response->getRequest());
        $response->exceptInput('hello');
        $this->assertSame(['foo' => 'bar'], $request->only(['foo']));
        $this->assertSame($response->getSession()->getFlash('foo'), 'bar');
    }

    public function testExceptInputMustHasAtLeastOneArg(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Method exceptInput need at least one arg.');

        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWith());
        $this->assertInstanceOf(ISession::class, $response->getSession());
        $this->assertNull($response->getRequest());

        $response->setRequest($this->mockRequest(['foo' => 'bar'], []));
        $this->assertInstanceOf(IRequest::class, $request = $response->getRequest());
        $response->exceptInput();
    }

    public function mokeSessionForWithReturnValue($name)
    {
        if ('inputs' === $name) {
            return [];
        }

        if (isset($GLOBALS['MOCK_SESSION_VALUE'])) {
            $value = $GLOBALS['MOCK_SESSION_VALUE'];
            unset($GLOBALS['MOCK_SESSION_VALUE']);

            return $value;
        }

        return 'bar';
    }

    protected function mokeSessionForWith(string $returnValue = 'bar'): ISession
    {
        $GLOBALS['MOCK_SESSION_VALUE'] = $returnValue;
        $session = $this->createMock(ISession::class);
        $session
            ->method('getFlash')
            ->willReturnCallback([$this, 'mokeSessionForWithReturnValue']);

        return $session;
    }

    protected function mokeSessionArrayForWith(array $returnValue = ['myinput' => 'world'])
    {
        $session = $this->createMock(ISession::class);
        $session
            ->method('getFlash')
            ->willReturn($returnValue);

        return $session;
    }

    protected function mokeSessionForWithError(array $data): ISession
    {
        $session = $this->createMock(ISession::class);
        $session
            ->method('getFlash')
            ->willReturn($data);

        return $session;
    }

    protected function mockRequest(array $returnValue, array $exceptReturnValue): IRequest
    {
        $request = $this->createMock(IRequest::class);
        $request
            ->method('only')
            ->willReturn($returnValue);
        $request
            ->method('except')
            ->willReturn($exceptReturnValue);
        $request
            ->method('input')
            ->willReturn([]);

        return $request;
    }
}
