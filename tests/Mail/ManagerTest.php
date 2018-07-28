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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Mail;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Mail\Manager;
use Leevel\Mvc\IView;
use Leevel\Option\Option;
use Swift_Message;
use Tests\TestCase;

/**
 * manager test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.28
 *
 * @version 1.0
 */
class ManagerTest extends TestCase
{
    public function testBaseUse()
    {
        $manager = $this->createManager();

        $manager->plain('Here is the message itself');

        $result = $manager->send(function (Swift_Message $message) {
            $message->setFrom(['foo@qq.com' => 'John Doe'])->

            setTo(['bar@qq.com' => 'A name'])->

            setBody('Here is the message itself');
        });

        $this->assertSame(1, $result);

        $this->assertSame([], $manager->failedRecipients());
    }

    protected function createManager()
    {
        $container = new Container();

        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'mail' => [
                'default' => 'nulls',

                'global_from' => [
                    'address' => null,
                    'name'    => null,
                ],

                'global_to' => [
                    'address' => null,
                    'name'    => null,
                ],

                'connect' => [
                    'nulls' => [
                        'driver' => 'nulls',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        $view = $this->createMock(IView::class);

        $container->singleton('view', $view);

        $event = $this->createMock(IDispatch::class);

        $container->singleton('event', $event);

        return $manager;
    }
}
