<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Mail;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Mail\Manager;
use Leevel\Option\Option;
use Leevel\View\Manager as ViewManager;
use Swift_Message;
use Tests\TestCase;

class ManagerTest extends TestCase
{
    public function testBaseUse(): void
    {
        $manager = $this->createManager();

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

    public function testSmtp(): void
    {
        $manager = $this->createManager('smtp');
        $manager->plain('Here is the message itself');
        $this->assertSame(1, 1);
    }

    public function testSendmail(): void
    {
        $manager = $this->createManager('sendmail');
        $manager->plain('Here is the message itself');
        $this->assertSame(1, 1);
    }

    protected function createManager(string $connect = 'test'): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'mail' => [
                'default'     => $connect,
                'global_from' => [
                    'address' => null,
                    'name'    => null,
                ],
                'global_to' => [
                    'address' => null,
                    'name'    => null,
                ],
                'connect' => [
                    'smtp' => [
                        'driver'     => 'smtp',
                        'host'       => 'smtp.qq.com',
                        'port'       => 587,
                        'username'   => '635750556@qq.com',
                        'password'   => 'demopassword',
                        'encryption' => 'ssl',
                    ],
                    'sendmail' => [
                        'driver' => 'sendmail',
                        'path'   => '/usr/sbin/sendmail -bs',
                    ],
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
}
