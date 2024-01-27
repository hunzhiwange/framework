<?php

declare(strict_types=1);

namespace Tests\Config\Proxy;

use Leevel\Config\Config;
use Leevel\Config\Proxy\Config as ProxyConfig;
use Leevel\Di\Container;
use Tests\TestCase;

final class ConfigTest extends TestCase
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
        $config = $this->createConfig();
        $container->singleton('config', function () use ($config): Config {
            return $config;
        });

        static::assertSame('testing', $config->get('app\\environment'));
        static::assertSame('testing', $config->get('environment'), 'Default namespace is app, so it equal app\\testing.');
        static::assertNull($config->get('hello'), 'The default namespace is app, so it equal app\\hello');
        static::assertNull($config->get('app\\hello'), 'The default namespace is app, so it equal app\\hello');
        static::assertSame($config->get('hello\\'), 'world');
        static::assertSame($config->get('hello\\*'), 'world');

        static::assertSame([
            'environment' => 'testing',
            'debug' => true,
        ], $config->get('app\\'));

        static::assertSame([
            'environment' => 'testing',
            'debug' => true,
        ], $config->get('app\\*'));

        static::assertFalse([
            'environment' => 'testing',
            'debug' => true,
        ] === $config->get('app'), 'The default namespace is app, so it equal app\\app');

        // namespace\sub.sub1.sub2
        static::assertSame($config->get('cache\\time_preset.foo'), 'bar');
        static::assertNull($config->get('cache\\time_preset.foo2'));
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $config = $this->createConfig();
        $container->singleton('config', function () use ($config): Config {
            return $config;
        });

        static::assertSame('testing', ProxyConfig::get('app\\environment'));
        static::assertSame('testing', ProxyConfig::get('environment'), 'Default namespace is app, so it equal app\\testing.');
        static::assertNull(ProxyConfig::get('hello'), 'The default namespace is app, so it equal app\\hello');
        static::assertNull(ProxyConfig::get('app\\hello'), 'The default namespace is app, so it equal app\\hello');
        static::assertSame(ProxyConfig::get('hello\\'), 'world');
        static::assertSame(ProxyConfig::get('hello\\*'), 'world');

        static::assertSame([
            'environment' => 'testing',
            'debug' => true,
        ], ProxyConfig::get('app\\'));

        static::assertSame([
            'environment' => 'testing',
            'debug' => true,
        ], ProxyConfig::get('app\\*'));

        static::assertFalse([
            'environment' => 'testing',
            'debug' => true,
        ] === ProxyConfig::get('app'), 'The default namespace is app, so it equal app\\app');

        // namespace\sub.sub1.sub2
        static::assertSame(ProxyConfig::get('cache\\time_preset.foo'), 'bar');
        static::assertNull(ProxyConfig::get('cache\\time_preset.foo2'));
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
