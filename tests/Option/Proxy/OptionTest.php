<?php

declare(strict_types=1);

namespace Tests\Option\Proxy;

use Leevel\Di\Container;
use Leevel\Option\Option;
use Leevel\Option\Proxy\Option as ProxyOption;
use Tests\TestCase;

class OptionTest extends TestCase
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

        $this->assertSame('testing', $option->get('app\\environment'));
        $this->assertSame('testing', $option->get('environment'), 'Default namespace is app, so it equal app\\testing.');
        $this->assertNull($option->get('hello'), 'The default namespace is app, so it equal app\\hello');
        $this->assertNull($option->get('app\\hello'), 'The default namespace is app, so it equal app\\hello');
        $this->assertSame($option->get('hello\\'), 'world');
        $this->assertSame($option->get('hello\\*'), 'world');

        $this->assertSame([
            'environment' => 'testing',
            'debug'       => true,
        ], $option->get('app\\'));

        $this->assertSame([
            'environment' => 'testing',
            'debug'       => true,
        ], $option->get('app\\*'));

        $this->assertFalse([
            'environment' => 'testing',
            'debug'       => true,
        ] === $option->get('app'), 'The default namespace is app, so it equal app\\app');

        // namespace\sub.sub1.sub2
        $this->assertSame($option->get('cache\\time_preset.foo'), 'bar');
        $this->assertNull($option->get('cache\\time_preset.foo2'));
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $option = $this->createOption();
        $container->singleton('option', function () use ($option): Option {
            return $option;
        });

        $this->assertSame('testing', ProxyOption::get('app\\environment'));
        $this->assertSame('testing', ProxyOption::get('environment'), 'Default namespace is app, so it equal app\\testing.');
        $this->assertNull(ProxyOption::get('hello'), 'The default namespace is app, so it equal app\\hello');
        $this->assertNull(ProxyOption::get('app\\hello'), 'The default namespace is app, so it equal app\\hello');
        $this->assertSame(ProxyOption::get('hello\\'), 'world');
        $this->assertSame(ProxyOption::get('hello\\*'), 'world');

        $this->assertSame([
            'environment' => 'testing',
            'debug'       => true,
        ], ProxyOption::get('app\\'));

        $this->assertSame([
            'environment' => 'testing',
            'debug'       => true,
        ], ProxyOption::get('app\\*'));

        $this->assertFalse([
            'environment' => 'testing',
            'debug'       => true,
        ] === ProxyOption::get('app'), 'The default namespace is app, so it equal app\\app');

        // namespace\sub.sub1.sub2
        $this->assertSame(ProxyOption::get('cache\\time_preset.foo'), 'bar');
        $this->assertNull(ProxyOption::get('cache\\time_preset.foo2'));
    }

    protected function createOption(): Option
    {
        $data = [
            'app' => [
                'environment' => 'testing',
                'debug'       => true,
            ],
            'cache' => [
                'expire'      => 86400,
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
