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

namespace Tests\Http;

use Leevel\Http\RedirectResponse;
use Leevel\Session\ISession;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Redirect Response",
 *     path="component/http/redirectresponse",
 *     zh-CN:description="QueryPHP 针对页面重定向可以直接返回一个 `\Leevel\Http\RedirectResponse` 响应对象。",
 * )
 */
class RedirectResponseTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="with 闪存一个数据片段到 SESSION",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWith(): void
    {
        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWith());
        $this->assertInstanceOf(ISession::class, $response->getSession());

        $response->with('foo', 'bar');
        $this->assertSame($response->getSession()->getFlash('foo'), 'bar');
    }

    /**
     * @api(
     *     zh-CN:title="withErrors 闪存错误信息",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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

    public function mokeSessionForWithReturnValue($name)
    {
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
        /** @var \Leevel\Session\ISession $session */
        $session = $this->createMock(ISession::class);
        $session
            ->method('getFlash')
            ->willReturnCallback([$this, 'mokeSessionForWithReturnValue']);

        return $session;
    }

    protected function mokeSessionForWithError(array $data): ISession
    {
        /** @var \Leevel\Session\ISession $session */
        $session = $this->createMock(ISession::class);
        $session
            ->method('getFlash')
            ->willReturn($data);

        return $session;
    }
}
