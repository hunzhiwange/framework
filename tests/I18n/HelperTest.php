<?php

declare(strict_types=1);

namespace Tests\I18n;

use Leevel\Di\Container;
use Leevel\I18n\Gettext;
use Leevel\I18n\II18n;
use Tests\I18n\Fixtures\DemoI18n;
use Tests\TestCase;

final class HelperTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        $this->tearDown();
    }

    #[\Override]
    protected function tearDown(): void
    {
        Container::singletons()->clear();
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
        self::assertSame('hello', $i18n->gettext('hello'));
        self::assertSame('hello foo', $i18n->gettext('hello %s', 'foo'));
        self::assertSame('hello 5', $i18n->gettext('hello %d', 5));

        $container = $this->createContainer();
        $container->singleton('i18n', static function () use ($i18n) {
            return $i18n;
        });

        self::assertSame('hello', Gettext::handle('hello'));
        self::assertSame('hello foo', Gettext::handle('hello %s', 'foo'));
        self::assertSame('hello 5', Gettext::handle('hello %d', 5));
    }

    public function test1(): void
    {
        $i18n = new DemoI18n();

        $container = $this->createContainer();
        $container->singleton('i18n', static function () use ($i18n) {
            return $i18n;
        });

        self::assertSame('hello', Gettext::handle('hello'));
        self::assertSame('hello foo', Gettext::handle('hello %s', 'foo'));
        self::assertSame('hello 5', Gettext::handle('hello %d', 5));
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
