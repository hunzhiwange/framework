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

namespace Tests\Leevel;

use Leevel;
use Leevel\Cache\Cache;
use Leevel\Cache\ICache;
use Leevel\Cache\IConnect as IConnectCache;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Encryption\IEncryption;
use Leevel\I18n\II18n;
use Leevel\Leevel\App as Apps;
use Leevel\Log\ILog;
use Leevel\Option\IOption;
use Leevel\Router\IUrl;
use Leevel\Session\ISession;
use Tests\TestCase;

/**
 * functions test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.22
 *
 * @version 1.0
 */
class FunctionsTest extends TestCase
{
    public function testBaseUse()
    {
        $app = Leevel::app();

        $this->assertInstanceof(IContainer::class, $app);
        $this->assertInstanceof(Container::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        // 等效
        $this->assertInstanceof(Apps::class, $app->make('app'));
        $this->assertSame('fooNotFound', $app->make('fooNotFound'));
        $this->assertInstanceof(Apps::class, Leevel::app('app'));
        $this->assertSame('fooNotFound', Leevel::app('fooNotFound'));
        $this->assertInstanceof(Apps::class, Leevel::app('app'));
        $this->assertSame('fooNotFound', Leevel::app('fooNotFound'));

        $app->clear();
    }

    public function testCallStaticException()
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Call to undefined function \\Leevel\\Leevel\\Helper\\not_found_callback()'
        );

        Leevel::notFoundCallback();
    }

    /**
     * @dataProvider envProvider
     *
     * @param string $name
     * @param mixed  $value
     * @param mixed  $envValue
     */
    public function testEnv(string $name, $value, $envValue)
    {
        $name = 'test_env_'.$name;

        putenv($name.'='.$value);

        $this->assertSame($envValue, Leevel::env($name));
    }

    public function envProvider()
    {
        return [
            ['bar', 'true', true],
            ['bar', '(true)', true],
            ['bar', 'false', false],
            ['bar', '(false)', false],
            ['bar', 'empty', ''],
            ['bar', '(empty)', ''],
            ['bar', 'null', null],
            ['bar', '(null)', null],
            ['bar', '"hello"', 'hello'],
            ['bar', "'hello'", "'hello'"],
            ['bar', true, '1'],
            ['bar', false, ''],
            ['bar', 1, '1'],
            ['bar', '', ''],
        ];
    }

    public function testEnvWithValue()
    {
        $this->assertNull(Leevel::env('testNotFound'));
        $this->assertSame('foo', Leevel::env('testNotFound', 'foo'));
        $this->assertSame('e10adc3949ba59abbe56e057f20f883e', Leevel::env('foo', function () {
            return md5('123456');
        }));
        $this->assertSame('__fooBar', Leevel::env('foo', '__fooBar'));
    }

    public function testLog()
    {
        $log = $this->createMock(ILog::class);

        $log->method('log')->willReturn(null);
        $this->assertNull($log->log(ILog::INFO, 'bar', []));

        $app = Apps::singletons();
        $app->clear();

        $app->singleton(ILog::class, function () use ($log) {
            return $log;
        });

        $this->assertInstanceof(ILog::class, Leevel::log());
        $this->assertNull(Leevel::log('bar', [], ILog::INFO));

        $app->clear();
    }

    public function testOption()
    {
        $option = $this->createMock(IOption::class);

        $option->method('set')->willReturn(null);
        $this->assertNull($option->set(['foo' => 'bar']));

        $option->method('get')->willReturn('bar');
        $this->assertSame('bar', $option->get('foo'));

        $app = Apps::singletons();
        $app->clear();

        $app->singleton(IOption::class, function () use ($option) {
            return $option;
        });

        $this->assertInstanceof(IOption::class, Leevel::option());
        $this->assertNull(Leevel::option(['foo' => 'bar']));
        $this->assertSame('bar', Leevel::option('foo'));

        $app->clear();
    }

    public function testCache()
    {
        $cache = $this->createMock(IConnectCache::class);

        $cache->method('set')->willReturn(null);
        $this->assertNull($cache->set('foo', 'bar'));

        $cache->method('get')->willReturn('bar');
        $this->assertSame('bar', $cache->get('foo'));

        $cache = new Cache($cache);
        $app = Apps::singletons();
        $app->clear();

        $app->singleton(ICache::class, function () use ($cache) {
            return $cache;
        });

        $this->assertInstanceof(ICache::class, Leevel::cache());
        $this->assertNull(Leevel::cache(['foo' => 'bar']));
        $this->assertSame('bar', Leevel::cache('foo'));

        $app->clear();
    }

    public function testEncryptAndEecrypt()
    {
        $encryption = $this->createMock(IEncryption::class);

        $encryption->method('encrypt')->willReturn('foobar-helloworld');
        $this->assertSame('foobar-helloworld', $encryption->encrypt('foo', 3600));

        $encryption->method('decrypt')->willReturn('foo');
        $this->assertSame('foo', $encryption->decrypt('foobar-helloworld'));

        $app = Apps::singletons();
        $app->clear();

        $app->singleton(IEncryption::class, function () use ($encryption) {
            return $encryption;
        });

        $this->assertSame('foobar-helloworld', Leevel::encrypt('foo', 3600));
        $this->assertSame('foo', Leevel::decrypt('foobar-helloworld'));

        $app->clear();
    }

    public function testSession()
    {
        $session = $this->createMock(ISession::class);

        $session->method('put')->willReturn(null);
        $this->assertNull($session->put(['foo' => 'bar']));

        $session->method('get')->willReturn('bar');
        $this->assertSame('bar', $session->get('foo'));

        $app = Apps::singletons();
        $app->clear();

        $app->singleton(ISession::class, function () use ($session) {
            return $session;
        });

        $this->assertInstanceof(ISession::class, Leevel::session());
        $this->assertNull(Leevel::session(['foo' => 'bar']));
        $this->assertSame('bar', Leevel::session('foo'));

        $app->clear();
    }

    public function testFlash()
    {
        $session = $this->createMock(ISession::class);

        $session->method('flash')->willReturn(null);
        $this->assertNull($session->flashs(['foo' => 'bar']));

        $session->method('getFlash')->willReturn('bar');
        $this->assertSame('bar', $session->getFlash('foo'));

        $app = Apps::singletons();
        $app->clear();

        $app->singleton(ISession::class, function () use ($session) {
            return $session;
        });

        $this->assertNull(Leevel::flash(['foo' => 'bar']));
        $this->assertSame('bar', Leevel::flash('foo'));

        $app->clear();
    }

    public function testUrl()
    {
        $url = $this->createMock(IUrl::class);

        $url->method('make')->willReturn('/goods?foo=bar');
        $this->assertSame('/goods?foo=bar', $url->make('/goods', ['foo' => 'bar']));

        $app = Apps::singletons();
        $app->clear();

        $app->singleton(IUrl::class, function () use ($url) {
            return $url;
        });

        $this->assertSame('/goods?foo=bar', Leevel::url('/goods', ['foo' => 'bar']));

        $app->clear();
    }

    public function testGettextWithI18n()
    {
        $i18n = $this->createMock(II18n::class);

        $map = [
            ['hello', 'hello'],
            ['hello %s', 'foo', 'hello foo'],
            ['hello %d', 5, 'hello 5'],
        ];

        $i18n->method('gettext')->will($this->returnValueMap($map));
        $this->assertSame('hello', $i18n->gettext('hello'));
        $this->assertSame('hello foo', $i18n->gettext('hello %s', 'foo'));
        $this->assertSame('hello 5', $i18n->gettext('hello %d', 5));

        $app = Apps::singletons();
        $app->clear();

        $app->singleton(II18n::class, function () use ($i18n) {
            return $i18n;
        });

        $this->assertSame('hello', Leevel::__('hello'));
        $this->assertSame('hello foo', Leevel::__('hello %s', 'foo'));
        $this->assertSame('hello 5', Leevel::__('hello %d', 5));

        $app->clear();
    }
}
