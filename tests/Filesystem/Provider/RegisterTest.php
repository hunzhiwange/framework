<?php

declare(strict_types=1);

namespace Tests\Filesystem\Provider;

use League\Flysystem\Filesystem as LeagueFilesystem;
use Leevel\Config\Config;
use Leevel\Di\Container;
use Leevel\Filesystem\Provider\Register;
use Tests\TestCase;

final class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // filesystems
        $manager = $container->make('filesystems');
        $path = __DIR__.'/forRegister';
        $this->assertInstanceof(LeagueFilesystem::class, $manager->getFilesystem());
        $manager->write('helloregister.txt', 'register');
        $file = $path.'/helloregister.txt';
        static::assertTrue(is_file($file));
        static::assertSame('register', file_get_contents($file));
        unlink($file);
        rmdir($path);

        // filesystem
        $local = $container->make('filesystem');
        $path = __DIR__.'/forRegister';
        $this->assertInstanceof(LeagueFilesystem::class, $local->getFilesystem());
        $local->write('helloregister.txt', 'register');
        $file = $path.'/helloregister.txt';
        static::assertTrue(is_file($file));
        static::assertSame('register', file_get_contents($file));
        unlink($file);
        rmdir($path);
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $config = new Config([
            'filesystem' => [
                'default' => 'local',
                'connect' => [
                    'local' => [
                        'driver' => 'local',
                        'path' => __DIR__.'/forRegister',
                    ],
                ],
            ],
        ]);

        $container->singleton('config', $config);

        return $container;
    }
}
