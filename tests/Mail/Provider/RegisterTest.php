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

namespace Tests\Mail\Provider;

use Leevel\Di\Container;
use Leevel\Event\IDispatch;
use Leevel\Mail\Provider\Register;
use Leevel\Option\Option;
use Leevel\View\Manager as ViewManager;
use Swift_Message;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // mails
        $manager = $container->make('mails');
        $manager->plain('Here is the message itself');
        $result = $manager->flush(function (Swift_Message $message) {
            $message
                ->setFrom(['foo@qq.com' => 'John Doe'])
                ->setTo(['bar@qq.com' => 'A name'])
                ->setBody('Here is the message itself');
        });
        $this->assertSame(1, $result);

        // mail
        $test = $container->make('mail');
        $test->plain('Here is the message itself');
        $result = $test->flush(function (Swift_Message $message) {
            $message
                ->setFrom(['foo@qq.com' => 'John Doe'])
                ->setTo(['bar@qq.com' => 'A name'])
                ->setBody('Here is the message itself');
        });
        $this->assertSame(1, $result);
    }

    protected function createContainer(): Container
    {
        $container = new Container();

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

        $view = $this->createViewManager();
        $container->singleton('views', $view);

        $event = $this->createMock(IDispatch::class);
        $container->singleton('event', $event);

        return $container;
    }

    protected function createViewManager(): ViewManager
    {
        return $this->createMock(ViewManager::class);
    }
}
