<?php

declare(strict_types=1);

namespace Tests\Session\Provider;

use Leevel\Config\Config;
use Leevel\Di\Container;
use Leevel\Session\Provider\Register;
use Tests\TestCase;

final class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // sessions
        $manager = $container->make('sessions');

        self::assertFalse($manager->isStart());
        self::assertSame('', $manager->getId());
        self::assertSame('UID', $manager->getName());

        $manager->start();
        self::assertTrue($manager->isStart());

        $manager->set('hello', 'world');
        self::assertSame(['hello' => 'world'], $manager->all());
        self::assertTrue($manager->has('hello'));
        self::assertSame('world', $manager->get('hello'));

        $manager->delete('hello');
        self::assertSame([], $manager->all());
        self::assertFalse($manager->has('hello'));
        self::assertNull($manager->get('hello'));

        $manager->start();
        self::assertTrue($manager->isStart());

        // session
        $test = $container->make('session');
        self::assertTrue($test->isStart());

        $test->set('hello', 'world');
        self::assertSame(['hello' => 'world'], $test->all());
        self::assertTrue($test->has('hello'));
        self::assertSame('world', $test->get('hello'));

        $test->delete('hello');
        self::assertSame([], $test->all());
        self::assertFalse($test->has('hello'));
        self::assertNull($test->get('hello'));

        $test->start();
        self::assertTrue($test->isStart());
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $config = new Config([
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

        $container->singleton('config', $config);

        return $container;
    }
}
