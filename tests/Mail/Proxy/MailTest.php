<?php

declare(strict_types=1);

namespace Tests\Mail\Proxy;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Mail\Manager;
use Leevel\Mail\Proxy\Mail;
use Leevel\Option\Option;
use Leevel\View\Manager as ViewManager;
use Swift_Message;
use Tests\TestCase;

class MailTest extends TestCase
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
        $manager = $this->createManager($container);
        $container->singleton('mails', function () use ($manager): Manager {
            return $manager;
        });

        $manager->plain('Here is the message itself');

        $result = $manager->flush(function (Swift_Message $message) {
            $message
                ->setFrom(['foo@qq.com' => 'John Doe'])
                ->setTo(['bar@qq.com' => 'A name'])
                ->setBody('Here is the message itself');
        });

        $this->assertSame(1, $result);
        $this->assertSame([], $manager->failedRecipients());
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('mails', function () use ($manager): Manager {
            return $manager;
        });

        Mail::plain('Here is the message itself');

        $result = Mail::flush(function (Swift_Message $message) {
            $message
                ->setFrom(['foo@qq.com' => 'John Doe'])
                ->setTo(['bar@qq.com' => 'A name'])
                ->setBody('Here is the message itself');
        });

        $this->assertSame(1, $result);
        $this->assertSame([], Mail::failedRecipients());
    }

    protected function createManager(Container $container): Manager
    {
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'mail' => [
                'default'     => 'test',
                'global_from' => [
                    'address' => null,
                    'name'    => null,
                ],
                'global_to' => [
                    'address' => null,
                    'name'    => null,
                ],
                'connect' => [
                    'test' => [
                        'driver' => 'test',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);
        $view = $this->createMock(ViewManager::class);
        $container->singleton('views', $view);
        $event = $this->createMock(IDispatch::class);
        $container->singleton('event', $event);

        return $manager;
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
