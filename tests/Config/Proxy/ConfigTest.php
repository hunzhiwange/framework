<?php

declare(strict_types=1);

namespace Tests\Config\Proxy;

use Leevel\Config\Config;
use Leevel\Config\Proxy\Config as ProxyConfig;
use Leevel\Di\Container;
use Tests\TestCase;

final class ConfigTest extends TestCase
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

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $config = $this->createConfig();
        $container->singleton('config', static function () use ($config): Config {
            return $config;
        });

        self::assertSame('testing', $config->get('app\\environment'));
        self::assertSame('testing', $config->get('environment'), 'Default namespace is app, so it equal app\\testing.');
        self::assertNull($config->get('hello'), 'The default namespace is app, so it equal app\\hello');
        self::assertNull($config->get('app\\hello'), 'The default namespace is app, so it equal app\\hello');
        self::assertSame($config->get('hello\\'), 'world');
        self::assertSame($config->get('hello\\*'), 'world');

        self::assertSame([
            'environment' => 'testing',
            'debug' => true,
        ], $config->get('app\\'));

        self::assertSame([
            'environment' => 'testing',
            'debug' => true,
        ], $config->get('app\\*'));

        self::assertFalse([
            'environment' => 'testing',
            'debug' => true,
        ] === $config->get('app'), 'The default namespace is app, so it equal app\\app');

        // namespace\sub.sub1.sub2
        self::assertSame($config->get('cache\\time_preset.foo'), 'bar');
        self::assertNull($config->get('cache\\time_preset.foo2'));
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $config = $this->createConfig();
        $container->singleton('config', static function () use ($config): Config {
            return $config;
        });

        self::assertSame('testing', ProxyConfig::get('app\\environment'));
        self::assertSame('testing', ProxyConfig::get('environment'), 'Default namespace is app, so it equal app\\testing.');
        self::assertNull(ProxyConfig::get('hello'), 'The default namespace is app, so it equal app\\hello');
        self::assertNull(ProxyConfig::get('app\\hello'), 'The default namespace is app, so it equal app\\hello');
        self::assertSame(ProxyConfig::get('hello\\'), 'world');
        self::assertSame(ProxyConfig::get('hello\\*'), 'world');

        self::assertSame([
            'environment' => 'testing',
            'debug' => true,
        ], ProxyConfig::get('app\\'));

        self::assertSame([
            'environment' => 'testing',
            'debug' => true,
        ], ProxyConfig::get('app\\*'));

        self::assertFalse([
            'environment' => 'testing',
            'debug' => true,
        ] === ProxyConfig::get('app'), 'The default namespace is app, so it equal app\\app');

        // namespace\sub.sub1.sub2
        self::assertSame(ProxyConfig::get('cache\\time_preset.foo'), 'bar');
        self::assertNull(ProxyConfig::get('cache\\time_preset.foo2'));
    }

    protected function createConfig(): Config
    {
        $data = [
            'app' => [
                'environment' => 'testing',
                'debug' => true,
            ],
            'cache' => [
                'expire' => 86400,
                'time_preset' => [
                    'foo' => 'bar',
                ],
            ],
            'hello' => 'world',
        ];

        return new Config($data);
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
