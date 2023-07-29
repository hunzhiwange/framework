<?php

declare(strict_types=1);

namespace Tests\Option\Proxy;

use Leevel\Di\Container;
use Leevel\Option\Option;
use Leevel\Option\Proxy\Option as ProxyOption;
use Tests\TestCase;

/**
 * @internal
 */
final class OptionTest extends TestCase
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
        $option = $this->createOption();
        $container->singleton('option', function () use ($option): Option {
            return $option;
        });

        static::assertSame('testing', $option->get('app\\environment'));
        static::assertSame('testing', $option->get('environment'), 'Default namespace is app, so it equal app\\testing.');
        static::assertNull($option->get('hello'), 'The default namespace is app, so it equal app\\hello');
        static::assertNull($option->get('app\\hello'), 'The default namespace is app, so it equal app\\hello');
        static::assertSame($option->get('hello\\'), 'world');
        static::assertSame($option->get('hello\\*'), 'world');

        static::assertSame([
            'environment' => 'testing',
            'debug' => true,
        ], $option->get('app\\'));

        static::assertSame([
            'environment' => 'testing',
            'debug' => true,
        ], $option->get('app\\*'));

        static::assertFalse([
            'environment' => 'testing',
            'debug' => true,
        ] === $option->get('app'), 'The default namespace is app, so it equal app\\app');

        // namespace\sub.sub1.sub2
        static::assertSame($option->get('cache\\time_preset.foo'), 'bar');
        static::assertNull($option->get('cache\\time_preset.foo2'));
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $option = $this->createOption();
        $container->singleton('option', function () use ($option): Option {
            return $option;
        });

        static::assertSame('testing', ProxyOption::get('app\\environment'));
        static::assertSame('testing', ProxyOption::get('environment'), 'Default namespace is app, so it equal app\\testing.');
        static::assertNull(ProxyOption::get('hello'), 'The default namespace is app, so it equal app\\hello');
        static::assertNull(ProxyOption::get('app\\hello'), 'The default namespace is app, so it equal app\\hello');
        static::assertSame(ProxyOption::get('hello\\'), 'world');
        static::assertSame(ProxyOption::get('hello\\*'), 'world');

        static::assertSame([
            'environment' => 'testing',
            'debug' => true,
        ], ProxyOption::get('app\\'));

        static::assertSame([
            'environment' => 'testing',
            'debug' => true,
        ], ProxyOption::get('app\\*'));

        static::assertFalse([
            'environment' => 'testing',
            'debug' => true,
        ] === ProxyOption::get('app'), 'The default namespace is app, so it equal app\\app');

        // namespace\sub.sub1.sub2
        static::assertSame(ProxyOption::get('cache\\time_preset.foo'), 'bar');
        static::assertNull(ProxyOption::get('cache\\time_preset.foo2'));
    }

    protected function createOption(): Option
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

        return new Option($data);
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
