<?php

declare(strict_types=1);

namespace Tests\I18n\Proxy;

use Leevel\Di\Container;
use Leevel\I18n\I18n;
use Leevel\I18n\Proxy\I18n as ProxyI18n;
use Tests\TestCase;

final class I18nTest extends TestCase
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
        $container->singleton('i18n', function (): I18n {
            return new I18n('zh-CN');
        });

        static::assertSame('zh-CN', ProxyI18n::getI18n());
        static::assertSame([
            'zh-CN' => [],
        ], ProxyI18n::all());
        static::assertSame('中国语言', ProxyI18n::gettext('中国语言'));
        static::assertSame('中国人语言', ProxyI18n::gettext('中国%s语言', '人'));
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
