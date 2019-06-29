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

namespace Tests\Kernel;

use Leevel;
use Leevel\Cache\ICache;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Encryption\IEncryption;
use Leevel\I18n\II18n;
use Leevel\Kernel\App;
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

        $app = Leevel::app();
        $container->alias([
            'app' => [
                'Leevel\\Leevel\\App',
                'Leevel\\Kernel\\IApp',
            ],
        ]);

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(App::class, $app);

        // 等效
        $this->assertInstanceof(App::class, $container->make('app'));
        $this->assertSame('fooNotFound', $container->make('fooNotFound'));
        $this->assertInstanceof(App::class, Leevel::app());

        $container->clear();
    }

    public function testCallStaticException(): void
    {
        $this->expectException(\Leevel\Support\FunctionNotFoundException::class);
        $this->expectExceptionMessage(
            'Call to undefined function Leevel\\Not_found_callback\\Helper\\not_found_callback()'
        );

        $container = $this->createContainer();

        Leevel::notFoundCallback();
    }

    /**
     * @dataProvider envProvider
     *
     * @param string $name
     * @param mixed  $value
     * @param mixed  $envValue
     */
    public function testEnv(string $name, $value, $envValue): void
    {
        $name = 'test_env_'.$name;

        putenv($name.'='.$value);

        $this->assertSame($envValue, Leevel::env($name));
    }

    public function envProvider(): array
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

    public function testEnvFalse(): void
    {
        $this->assertSame('default message', Leevel::env('not_found_env', 'default message'));
        $this->assertNull(Leevel::env('not_found_env'));
    }

    public function testLog(): void
    {
        $log = $this->createMock(ILog::class);

        $this->assertNull($log->log(ILog::INFO, 'bar', []));

        $container = $this->createContainer();

        $container->singleton('logs', function () use ($log) {
            return $log;
        });

        $this->assertInstanceof(ILog::class, Leevel::log());
        $this->assertNull(Leevel::logRecord('bar', [], ILog::INFO));

        $container->clear();
    }

    public function testOption(): void
    {
        $option = $this->createMock(IOption::class);

        $this->assertNull($option->set(['foo' => 'bar']));

        $option->method('get')->willReturn('bar');
        $this->assertSame('bar', $option->get('foo'));

        $container = $this->createContainer();

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertInstanceof(IOption::class, Leevel::option());
        $this->assertNull(Leevel::optionSet(['foo' => 'bar']));
        $this->assertSame('bar', Leevel::optionGet('foo'));

        $container->clear();
    }

    public function testCache(): void
    {
        $cache = $this->createMock(ICache::class);

        $this->assertNull($cache->set('foo', 'bar'));

        $cache->method('get')->willReturn('bar');
        $this->assertSame('bar', $cache->get('foo'));

        $container = $this->createContainer();

        $container->singleton('caches', function () use ($cache) {
            return $cache;
        });

        $this->assertInstanceof(ICache::class, Leevel::cache());
        $this->assertNull(Leevel::cacheSet(['foo' => 'bar']));
        $this->assertSame('bar', Leevel::cacheGet('foo'));

        $container->clear();
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

        $this->assertSame('foobar-helloworld', Leevel::encrypt('foo', 3600));
        $this->assertSame('foo', Leevel::decrypt('foobar-helloworld'));

        $container->clear();
    }

    public function testSession(): void
    {
        $session = $this->createMock(ISession::class);

        $this->assertNull($session->set('foo', 'bar'));

        $session->method('get')->willReturn('bar');
        $this->assertSame('bar', $session->get('foo'));

        $container = $this->createContainer();

        $container->singleton('sessions', function () use ($session) {
            return $session;
        });

        $this->assertInstanceof(ISession::class, Leevel::session());
        $this->assertNull(Leevel::sessionSet('foo', 'bar'));
        $this->assertSame('bar', Leevel::sessionGet('foo'));

        $container->clear();
    }

    public function testFlash(): void
    {
        $session = $this->createMock(ISession::class);

        $this->assertNull($session->flashs(['foo' => 'bar']));

        $session->method('getFlash')->willReturn('bar');
        $this->assertSame('bar', $session->getFlash('foo'));

        $container = $this->createContainer();

        $container->singleton('sessions', function () use ($session) {
            return $session;
        });

        $this->assertNull(Leevel::flashSet('foo', 'bar'));
        $this->assertSame('bar', Leevel::flashGet('foo'));

        $container->clear();
    }

    public function testUrl(): void
    {
        $url = $this->createMock(IUrl::class);

        $url->method('make')->willReturn('/goods?foo=bar');
        $this->assertSame('/goods?foo=bar', $url->make('/goods', ['foo' => 'bar']));

        $container = $this->createContainer();

        $container->singleton('url', function () use ($url) {
            return $url;
        });

        $this->assertSame('/goods?foo=bar', Leevel::url('/goods', ['foo' => 'bar']));

        $container->clear();
    }

    public function testGettextWithI18n(): void
    {
        $i18n = $this->createMock(II18n::class);

        $map = [
            ['hello', 'hello'],
            ['hello %s', 'foo', 'hello foo'],
            ['hello %d', 5, 'hello 5'],
        ];

        $i18n->method('gettext')->willReturnMap($map);
        $this->assertSame('hello', $i18n->gettext('hello'));
        $this->assertSame('hello foo', $i18n->gettext('hello %s', 'foo'));
        $this->assertSame('hello 5', $i18n->gettext('hello %d', 5));

        $container = $this->createContainer();

        $container->singleton('i18n', function () use ($i18n) {
            return $i18n;
        });

        $this->assertSame('hello', Leevel::gettext('hello'));
        $this->assertSame('hello foo', Leevel::gettext('hello %s', 'foo'));
        $this->assertSame('hello 5', Leevel::gettext('hello %d', 5));

        $container->clear();
    }

    public function testLeevelWithOtherAppMethod(): void
    {
        $container = $this->createContainer();
        $this->assertSame('/runtime', Leevel::runtimePath());
        $container->clear();
    }

    public function testLeevelWithOtherContainerMethod(): void
    {
        $container = $this->createContainer();
        $this->assertSame('foo', Leevel::make('foo'));
        $container->clear();
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();
        $container->instance('app', new App($container, ''));

        return $container;
    }
}
