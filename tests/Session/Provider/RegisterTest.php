<?php

declare(strict_types=1);

namespace Tests\Session\Provider;

use Leevel\Di\Container;
use Leevel\Option\Option;
use Leevel\Session\Provider\Register;
use Tests\TestCase;

/**
 * @internal
 */
final class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // sessions
        $manager = $container->make('sessions');

        static::assertFalse($manager->isStart());
        static::assertSame('', $manager->getId());
        static::assertSame('UID', $manager->getName());

        $manager->start();
        static::assertTrue($manager->isStart());

        $manager->set('hello', 'world');
        static::assertSame(['hello' => 'world'], $manager->all());
        static::assertTrue($manager->has('hello'));
        static::assertSame('world', $manager->get('hello'));

        $manager->delete('hello');
        static::assertSame([], $manager->all());
        static::assertFalse($manager->has('hello'));
        static::assertNull($manager->get('hello'));

        $manager->start();
        static::assertTrue($manager->isStart());

        // session
        $test = $container->make('session');
        static::assertTrue($test->isStart());

        $test->set('hello', 'world');
        static::assertSame(['hello' => 'world'], $test->all());
        static::assertTrue($test->has('hello'));
        static::assertSame('world', $test->get('hello'));

        $test->delete('hello');
        static::assertSame([], $test->all());
        static::assertFalse($test->has('hello'));
        static::assertNull($test->get('hello'));

        $test->start();
        static::assertTrue($test->isStart());
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $option = new Option([
            'session' => [
                'default' => 'test',
                'id' => null,
                'name' => 'UID',
                'expire' => 86400,
                'connect' => [
                    'test' => [
                        'driver' => 'test',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        return $container;
    }
}
