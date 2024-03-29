<?php

declare(strict_types=1);

namespace Tests\View\Provider;

use Leevel\Config\Config;
use Leevel\Di\Container;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App;
use Leevel\View\Manager;
use Leevel\View\Provider\Register;
use Tests\TestCase;

final class RegisterTest extends TestCase
{
    protected function tearDown(): void
    {
        Helper::deleteDirectory(__DIR__.'/cache_app');
    }

    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // views
        $manager = $container->make('views');
        $manager->setVar('foo', 'bar');
        $result = $manager->display('html_test');
        static::assertSame('hello html,bar.', $result);

        // alias
        $manager = $container->make(Manager::class);
        $manager->setVar('foo', 'newbar');
        $result = $manager->display('html_test');
        static::assertSame('hello html,newbar.', $result);

        // view
        $view = $container->make('view');
        $view->setVar('foo', 'newbarview');
        $result = $view->display('html_test');
        static::assertSame('hello html,newbarview.', $result);
    }

    protected function createContainer(): Container
    {
        $app = new ExtendApp($container = new Container(), '');
        $container->instance('app', $app);

        static::assertSame(__DIR__.'/assert', $app->themesPath());
        static::assertSame(__DIR__.'/cache_theme', $app->storagePath('theme'));

        $config = new Config([
            'view' => [
                'default' => 'html',
                'action_fail' => 'public/fail',
                'action_success' => 'public/success',
                'connect' => [
                    'html' => [
                        'driver' => 'html',
                        'suffix' => '.html',
                    ],
                ],
            ],
        ]);

        $container->singleton('config', $config);

        $request = new ExtendRequest();

        $container->singleton('request', $request);

        return $container;
    }
}

class ExtendApp extends App
{
    public function development(): bool
    {
        return true;
    }

    public function themesPath(string $path = ''): string
    {
        return __DIR__.'/assert';
    }

    public function storagePath(string $path = ''): string
    {
        return __DIR__.'/cache_'.$path;
    }
}

class ExtendRequest
{
}
