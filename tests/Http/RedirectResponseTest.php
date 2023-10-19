<?php

declare(strict_types=1);

namespace Tests\Http;

use Leevel\Http\RedirectResponse;
use Leevel\Session\ISession;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'Redirect Response',
    'path' => 'component/http/redirectresponse',
    'zh-CN:description' => <<<'EOT'
QueryPHP 针对页面重定向可以直接返回一个 `\Leevel\Http\RedirectResponse` 响应对象。
EOT,
])]
final class RedirectResponseTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'with 闪存一个数据片段到 SESSION',
    ])]
    public function testWith(): void
    {
        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWith());
        static::assertInstanceOf(ISession::class, $response->getSession());

        $response->with('foo', 'bar');
        static::assertSame($response->getSession()->getFlash('foo'), 'bar');
    }

    #[Api([
        'zh-CN:title' => 'withErrors 闪存错误信息',
    ])]
    public function testWithError(): void
    {
        $errorsDefault = [
            'name' => 'less than 6',
            'age' => 'must be 18',
        ];

        $errorsCustom = [
            'foo' => 'bar is error',
        ];
        $data = ['default' => $errorsDefault, 'custom' => $errorsCustom];
        $response = new RedirectResponse('foo.bar');
        $response->setSession($this->mokeSessionForWithError($data));
        static::assertInstanceOf(ISession::class, $response->getSession());
        $response->withErrors($errorsDefault);
        $response->withErrors($errorsCustom, 'custom');

        static::assertSame($response->getSession()->getFlash('errors'), $data);
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
            ->willReturnCallback([$this, 'mokeSessionForWithReturnValue'])
        ;

        return $session;
    }

    protected function mokeSessionForWithError(array $data): ISession
    {
        /** @var \Leevel\Session\ISession $session */
        $session = $this->createMock(ISession::class);
        $session
            ->method('getFlash')
            ->willReturn($data)
        ;

        return $session;
    }
}
