<?php

declare(strict_types=1);

namespace Tests\I18n;

use Leevel\Di\Container;
use function Leevel\I18n\gettext;
use Leevel\I18n\II18n;
use Tests\TestCase;

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

        $this->assertSame('hello', func(fn () => gettext('hello')));
        $this->assertSame('hello foo', func(fn () => gettext('hello %s', 'foo')));
        $this->assertSame('hello 5', func(fn () => gettext('hello %d', 5)));
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
