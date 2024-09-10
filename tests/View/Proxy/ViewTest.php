<?php

declare(strict_types=1);

namespace Tests\View\Proxy;

use Leevel\Config\Config;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Kernel\App;
use Leevel\View\Manager;
use Leevel\View\Proxy\View as ProxyView;
use Tests\TestCase;

final class ViewTest extends TestCase
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
        $manager = $this->createManager();
        $this->assertInstanceof(Manager::class, $manager);

        $container = $this->createContainer();
        $container->singleton('views', static function () use ($manager): Manager {
            return $manager;
        });

        $manager->setVar('hello', 'world');
        self::assertSame('world', $manager->getVar('hello'));
    }

    public function testProxy(): void
    {
        $manager = $this->createManager();
        $this->assertInstanceof(Manager::class, $manager);

        $container = $this->createContainer();
        $container->singleton('views', static function () use ($manager): Manager {
            return $manager;
        });

        ProxyView::proxy()->setVar('hello', 'world');
        self::assertSame('world', ProxyView::proxy()->getVar('hello'));
        self::assertSame('world', $manager->getVar('hello'));
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }

    protected function createManager(string $connect = 'html'): Manager
    {
        $app = new ExtendApp($container = new Container(), '');
        $container->instance('app', $app);

        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        self::assertSame(__DIR__.'/assert', $app->themesPath());
        self::assertSame(__DIR__.'/cache_theme', $app->storagePath('theme'));

        $config = new Config([
            'view' => [
                'default' => $connect,
                'action_fail' => 'public/fail',
                'action_success' => 'public/success',
                'connect' => [
                    'html' => [
                        'driver' => 'html',
                        'suffix' => '.html',
                    ],
                    'phpui' => [
                        'driver' => 'phpui',
                        'suffix' => '.php',
                    ],
                ],
            ],
        ]);
        $container->singleton('config', $config);

        $request = new ExtendRequest();
        $container->singleton('request', $request);

        return $manager;
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

class ExtendRequest {}
