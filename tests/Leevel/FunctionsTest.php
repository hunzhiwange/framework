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

use Leevel as Leevels;
use Leevel\Cache\Cache;
use Leevel\Cache\ICache;
use Leevel\Cache\IConnect as IConnectCache;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Encryption\IEncryption;
use Leevel\I18n\II18n;
use Leevel\Kernel\IApp;
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
    }

    public function testCallStaticException()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Method `notFoundCallback` is not exits.'
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

        $app = new App3();

        $app->singleton('logs', function () use ($log) {
            return $log;
        });

        Leevel2::setApp($app);

        $this->assertInstanceof(ILog::class, Leevel2::log());
        $this->assertNull(Leevel2::log('bar', [], ILog::INFO));
    }

    public function testOption()
    {
        $option = $this->createMock(IOption::class);

        $option->method('set')->willReturn(null);
        $this->assertNull($option->set(['foo' => 'bar']));

        $option->method('get')->willReturn('bar');
        $this->assertSame('bar', $option->get('foo'));

        $app = new App3();

        $app->singleton('option', function () use ($option) {
            return $option;
        });

        Leevel2::setApp($app);

        $this->assertInstanceof(IOption::class, Leevel2::option());
        $this->assertNull(Leevel2::option(['foo' => 'bar']));
        $this->assertSame('bar', Leevel2::option('foo'));
    }

    public function testCache()
    {
        $cache = $this->createMock(IConnectCache::class);

        $cache->method('set')->willReturn(null);
        $this->assertNull($cache->set('foo', 'bar'));

        $cache->method('get')->willReturn('bar');
        $this->assertSame('bar', $cache->get('foo'));

        $cache = new Cache($cache);

        $app = new App3();

        $app->singleton('caches', function () use ($cache) {
            return $cache;
        });

        Leevel2::setApp($app);

        $this->assertInstanceof(ICache::class, Leevel2::cache());
        $this->assertNull(Leevel2::cache(['foo' => 'bar']));
        $this->assertSame('bar', Leevel2::cache('foo'));
    }

    public function testEncryptAndEecrypt()
    {
        $encryption = $this->createMock(IEncryption::class);

        $encryption->method('encrypt')->willReturn('foobar-helloworld');
        $this->assertSame('foobar-helloworld', $encryption->encrypt('foo', 3600));

        $encryption->method('decrypt')->willReturn('foo');
        $this->assertSame('foo', $encryption->decrypt('foobar-helloworld'));

        $app = new App3();

        $app->singleton('encryption', function () use ($encryption) {
            return $encryption;
        });

        Leevel2::setApp($app);

        $this->assertSame('foobar-helloworld', Leevel2::encrypt('foo', 3600));
        $this->assertSame('foo', Leevel2::decrypt('foobar-helloworld'));
    }

    public function testSession()
    {
        $session = $this->createMock(ISession::class);

        $session->method('put')->willReturn(null);
        $this->assertNull($session->put(['foo' => 'bar']));

        $session->method('get')->willReturn('bar');
        $this->assertSame('bar', $session->get('foo'));

        $app = new App3();

        $app->singleton('sessions', function () use ($session) {
            return $session;
        });

        Leevel2::setApp($app);

        $this->assertInstanceof(ISession::class, Leevel2::session());
        $this->assertNull(Leevel2::session(['foo' => 'bar']));
        $this->assertSame('bar', Leevel2::session('foo'));
    }

    public function testFlash()
    {
        $session = $this->createMock(ISession::class);

        $session->method('flash')->willReturn(null);
        $this->assertNull($session->flashs(['foo' => 'bar']));

        $session->method('getFlash')->willReturn('bar');
        $this->assertSame('bar', $session->getFlash('foo'));

        $app = new App3();

        $app->singleton('sessions', function () use ($session) {
            return $session;
        });

        Leevel2::setApp($app);

        $this->assertInstanceof(ISession::class, Leevel2::flash());
        $this->assertNull(Leevel2::flash(['foo' => 'bar']));
        $this->assertSame('bar', Leevel2::flash('foo'));
    }

    public function testUrl()
    {
        $url = $this->createMock(IUrl::class);

        $url->method('make')->willReturn('/goods?foo=bar');
        $this->assertSame('/goods?foo=bar', $url->make('/goods', ['foo' => 'bar']));

        $app = new App3();

        $app->singleton('url', function () use ($url) {
            return $url;
        });

        Leevel2::setApp($app);

        $this->assertSame('/goods?foo=bar', Leevel2::url('/goods', ['foo' => 'bar']));
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

        $app = new App3();

        $app->singleton('i18n', function () use ($i18n) {
            return $i18n;
        });

        Leevel2::setApp($app);

        $this->assertSame('hello', Leevel2::gettext('hello'));
        $this->assertSame('hello foo', Leevel2::gettext('hello %s', 'foo'));
        $this->assertSame('hello 5', Leevel2::gettext('hello %d', 5));

        $this->assertSame('hello', Leevel2::__('hello'));
        $this->assertSame('hello foo', Leevel2::__('hello %s', 'foo'));
        $this->assertSame('hello 5', Leevel2::__('hello %d', 5));
    }
}

class App2 extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class App3 extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class Leevel extends Leevels
{
    protected static function singletons(): IContainer
    {
        return new App2($appPath = __DIR__.'/app');
    }
}

class Leevel2 extends Leevels
{
    protected static $app;

    public static function setApp(IApp $app)
    {
        self::$app = $app;
    }

    protected static function singletons(): IContainer
    {
        return self::$app;
    }
}
